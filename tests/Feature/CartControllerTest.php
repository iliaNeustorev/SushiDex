<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use App\Services\Cart\CartService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class CartControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_open_an_empty_cart(): void
    {
        $this->get(route('cart.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Cart/Index')
                ->where('cart.items', [])
                ->where('cart.total_price', 0)
                ->has('cartDetails', 0));
    }

    public function test_user_can_update_quantity_and_remove_an_item_with_zero_quantity(): void
    {
        $user = User::factory()->create();
        $product = $this->product();

        $this->actingAs($user)
            ->putJson(route('cart.update'), [
                'product_id' => $product->id,
                'count' => 2,
            ])
            ->assertOk()
            ->assertJsonPath('cart.items.0.id', $product->id)
            ->assertJsonPath('cart.items.0.count', 2);

        $this->actingAs($user)
            ->putJson(route('cart.update'), [
                'product_id' => $product->id,
                'count' => 3,
            ])
            ->assertOk()
            ->assertJsonPath('cart.items.0.count', 3);

        $this->assertDatabaseHas('carts', [
            'user_id' => $user->id,
            'product_id' => $product->id,
            'count' => 3,
        ]);

        $this->actingAs($user)
            ->putJson(route('cart.update'), [
                'product_id' => $product->id,
                'count' => 0,
            ])
            ->assertOk()
            ->assertExactJson([
                'cart' => [
                    'items' => [],
                    'total_price' => 0,
                ],
            ]);

        $this->assertDatabaseMissing('carts', [
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);
    }

    public function test_user_can_clear_only_their_own_cart(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $product = $this->product();
        $otherProduct = $this->product('20.50', 'Salmon roll');
        $service = app(CartService::class);

        $service->put($user, $product, 2);
        $service->put($otherUser, $otherProduct, 4);

        $this->actingAs($user)
            ->delete(route('cart.destroy'))
            ->assertRedirect();

        $this->assertDatabaseMissing('carts', [
            'user_id' => $user->id,
        ]);
        $this->assertDatabaseHas('carts', [
            'user_id' => $otherUser->id,
            'product_id' => $otherProduct->id,
            'count' => 4,
        ]);
    }

    private function product(string $price = '10.25', string $title = 'California roll'): Product
    {
        $category = Category::firstOrCreate(
            ['url' => 'sushi-rolls'],
            ['title' => 'Sushi rolls'],
        );

        return Product::create([
            'title' => $title,
            'price' => $price,
            'category_id' => $category->id,
            'active' => true,
        ]);
    }
}
