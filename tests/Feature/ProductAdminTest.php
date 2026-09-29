<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class ProductAdminTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Gate::define('dev', fn (User $user) => true);
    }

    public function test_developer_can_create_update_and_delete_a_product(): void
    {
        $user = User::factory()->create();
        $category = Category::create(['url' => 'sushi-rolls', 'title' => 'Sushi rolls']);

        $response = $this->actingAs($user)->post(route('admin.products.store'), [
            'title' => 'California roll',
            'description' => null,
            'content' => null,
            'price' => '12.50',
            'old_price' => '15.00',
            'category_id' => $category->id,
            'active' => true,
        ]);

        $product = Product::sole();
        $response->assertRedirect(route('admin.products.edit', $product));

        $this->actingAs($user)->patch(route('admin.products.update', $product), [
            'title' => 'California premium',
            'description' => null,
            'content' => null,
            'price' => '13.50',
            'old_price' => null,
            'category_id' => $category->id,
            'active' => true,
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
        $user = User::factory()->create();

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
        $user = User::factory()->create();
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
        $user = User::factory()->create();
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
}
