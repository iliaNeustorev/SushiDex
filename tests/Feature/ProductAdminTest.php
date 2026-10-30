<?php

namespace Tests\Feature;

use App\Enums\System\Roles;
use App\Models\Cart;
use App\Models\Category;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ProductAdminTest extends TestCase
{
    use RefreshDatabase;

    public static function staffRoles(): array
    {
        return [
            'administrator' => [Roles::ADMIN],
            'developer' => [Roles::DEVELOPER],
        ];
    }

    #[DataProvider('staffRoles')]
    public function test_staff_can_create_update_and_delete_a_product(Roles $highestRole): void
    {
        $user = $this->userWithRoles($highestRole);
        $category = Category::create(['url' => 'sushi-rolls', 'title' => 'Sushi rolls']);

        $response = $this->actingAs($user)->post(route('admin.products.store'), [
            'title' => 'California roll',
            'description' => null,
            'content' => null,
            'price' => '12.50',
            'old_price' => '15.00',
            'category_id' => $category->id,
        ]);

        $product = Product::sole();
        $response->assertRedirect(route('admin.products.edit', $product));
        $this->assertDatabaseHas('products', ['id' => $product->id, 'active' => false]);

        $this->actingAs($user)->patch(route('admin.products.update', $product), [
            'title' => 'California premium',
            'description' => null,
            'content' => null,
            'price' => '13.50',
            'old_price' => null,
            'category_id' => $category->id,
        ])->assertRedirect();

        $this->assertDatabaseHas('products', ['id' => $product->id, 'title' => 'California premium']);

        Cart::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'count' => 2,
        ]);

        $this->actingAs($user)
            ->delete(route('admin.products.destroy', $product))
            ->assertRedirect(route('admin.products.index'));

        $this->assertSoftDeleted($product);
        $this->assertDatabaseMissing('carts', ['product_id' => $product->id]);
    }

    public function test_product_price_and_category_are_validated(): void
    {
        $user = $this->userWithRoles(Roles::DEVELOPER);

        $this->actingAs($user)->post(route('admin.products.store'), [
            'title' => 'Valid title',
            'description' => null,
            'content' => null,
            'price' => '-1',
            'old_price' => null,
            'category_id' => 999999,
        ])->assertSessionHasErrors(['price', 'category_id']);
    }

    public function test_developer_can_change_product_activity(): void
    {
        $user = $this->userWithRoles(Roles::DEVELOPER);
        $category = Category::create(['url' => 'sushi-rolls', 'title' => 'Sushi rolls']);
        $product = Product::create([
            'title' => 'California roll',
            'price' => '12.50',
            'category_id' => $category->id,
            'active' => true,
        ]);

        $this->actingAs($user)
            ->from(route('admin.products.index'))
            ->patch(route('admin.products.change-active', $product), [
                'active' => false,
            ])
            ->assertRedirect(route('admin.products.index'));

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'active' => false,
        ]);

        $this->actingAs($user)
            ->patch(route('admin.products.change-active', $product), [
                'active' => true,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'active' => true,
        ]);
    }

    public function test_product_activity_must_be_boolean(): void
    {
        $user = $this->userWithRoles(Roles::DEVELOPER);
        $category = Category::create(['url' => 'sushi-rolls', 'title' => 'Sushi rolls']);
        $product = Product::create([
            'title' => 'California roll',
            'price' => '12.50',
            'category_id' => $category->id,
            'active' => false,
        ]);

        $this->actingAs($user)
            ->patch(route('admin.products.change-active', $product), [
                'active' => 'invalid',
            ])
            ->assertSessionHasErrors('active');

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'active' => false,
        ]);
    }

    public function test_administrator_cannot_use_the_activity_endpoint_or_product_trash(): void
    {
        $user = $this->userWithRoles(Roles::ADMIN);
        $category = Category::create(['url' => 'sushi-rolls', 'title' => 'Sushi rolls']);
        $product = Product::create([
            'title' => 'California roll',
            'price' => '12.50',
            'category_id' => $category->id,
            'active' => false,
        ]);

        $this->actingAs($user)
            ->patch(route('admin.products.change-active', $product), ['active' => true])
            ->assertForbidden();

        $this->actingAs($user)->get(route('admin.product-trash.index'))->assertForbidden();
        $this->assertDatabaseHas('products', ['id' => $product->id, 'active' => false]);
    }

    public static function staffActivityChanges(): array
    {
        return [
            'administrator cannot enable' => [Roles::ADMIN, false],
            'administrator cannot disable' => [Roles::ADMIN, true],
            'developer cannot enable through the form' => [Roles::DEVELOPER, false],
            'developer cannot disable through the form' => [Roles::DEVELOPER, true],
        ];
    }

    #[DataProvider('staffActivityChanges')]
    public function test_regular_update_ignores_activity_changes(Roles $highestRole, bool $active): void
    {
        $user = $this->userWithRoles($highestRole);
        $category = Category::create(['url' => 'sushi-rolls', 'title' => 'Sushi rolls']);
        $product = Product::create([
            'title' => 'California roll',
            'price' => '12.50',
            'category_id' => $category->id,
            'active' => $active,
        ]);

        $this->actingAs($user)->patch(route('admin.products.update', $product), [
            'title' => 'California premium',
            'description' => null,
            'content' => null,
            'price' => '13.50',
            'old_price' => null,
            'category_id' => $category->id,
            'active' => ! $active,
        ])->assertRedirect()->assertSessionHasNoErrors();

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'title' => 'California premium',
            'active' => $active,
        ]);
    }

    #[DataProvider('staffRoles')]
    public function test_product_creation_ignores_activity_from_the_request(Roles $highestRole): void
    {
        $user = $this->userWithRoles($highestRole);
        $category = Category::create(['url' => 'sushi-rolls', 'title' => 'Sushi rolls']);

        $this->actingAs($user)->post(route('admin.products.store'), [
            'title' => 'California roll',
            'description' => null,
            'content' => null,
            'price' => '12.50',
            'old_price' => null,
            'category_id' => $category->id,
            'active' => true,
        ])->assertRedirect()->assertSessionHasNoErrors();

        $this->assertDatabaseHas('products', ['title' => 'California roll', 'active' => false]);
    }

    public function test_administrator_with_author_role_can_delete_a_product_image(): void
    {
        Storage::fake('public');
        $user = $this->userWithRoles(Roles::ADMIN);
        $category = Category::create(['url' => 'sushi-rolls', 'title' => 'Sushi rolls']);
        $product = Product::create([
            'title' => 'California roll',
            'price' => '12.50',
            'category_id' => $category->id,
            'active' => false,
        ]);
        $image = $product->images()->create(['path' => 'images/products/test.jpg', 'name' => 'test']);
        Storage::disk('public')->put($image->path, 'image contents');

        $this->actingAs($user)->delete(route('admin.images.destroy', $image))->assertRedirect();

        $this->assertDatabaseMissing('images', ['id' => $image->id]);
        Storage::disk('public')->assertMissing($image->path);
    }

    #[DataProvider('nonStaffRoles')]
    public function test_user_and_author_cannot_manage_products(Roles $highestRole): void
    {
        $user = $this->userWithRoles($highestRole);
        $category = Category::create(['url' => 'sushi-rolls', 'title' => 'Sushi rolls']);
        $product = Product::create([
            'title' => 'California roll',
            'price' => '12.50',
            'category_id' => $category->id,
            'active' => false,
        ]);

        $this->actingAs($user)->get(route('admin.products.index'))->assertForbidden();
        $this->get(route('admin.products.create'))->assertForbidden();
        $this->get(route('admin.products.edit', $product))->assertForbidden();
        $this->post(route('admin.products.store'), [])->assertForbidden();
        $this->patch(route('admin.products.update', $product), [])->assertForbidden();
        $this->delete(route('admin.products.destroy', $product))->assertForbidden();
        $this->assertNotSoftDeleted($product);
    }

    public static function nonStaffRoles(): array
    {
        return [
            'user' => [Roles::USER],
            'author' => [Roles::AUTHOR],
        ];
    }

    private function userWithRoles(Roles $highestRole): User
    {
        $user = User::factory()->create();
        $roles = match ($highestRole) {
            Roles::USER => [Roles::USER],
            Roles::AUTHOR => [Roles::USER, Roles::AUTHOR],
            Roles::ADMIN => [Roles::USER, Roles::AUTHOR, Roles::ADMIN],
            Roles::DEVELOPER => [Roles::USER, Roles::AUTHOR, Roles::ADMIN, Roles::DEVELOPER],
        };

        foreach ($roles as $roleName) {
            $role = Role::firstOrCreate(
                ['name' => $roleName->value],
                ['description' => $roleName->text()],
            );
            $user->roles()->attach($role);
        }

        return $user;
    }
}
