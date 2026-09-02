<?php

namespace Tests\Feature;

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
        ])->assertRedirect();

        $this->assertDatabaseHas('products', ['id' => $product->id, 'title' => 'California premium']);

        $this->actingAs($user)
            ->delete(route('admin.products.destroy', $product))
            ->assertRedirect(route('admin.products.index'));

        $this->assertSoftDeleted($product);
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
}
