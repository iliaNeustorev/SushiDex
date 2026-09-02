<?php

namespace Tests\Feature;

use App\Enums\Orders\Status as OrderStatus;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use App\Services\Cart\CartService;
use App\Services\Order\OrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartOrderServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_cart_is_owned_by_user_and_replaces_quantity(): void
    {
        $user = User::factory()->create();
        $product = $this->product('10.25');
        $service = app(CartService::class);

        $service->put($user, $product, 2);
        $item = $service->put($user, $product, 3);

        $this->assertSame(1, $user->products()->count());
        $this->assertSame($product->id, $user->products()->sole()->id);
        $this->assertSame($user->id, $product->users()->sole()->id);
        $this->assertSame(3, $item->count);
    }

    public function test_checkout_calculates_total_snapshots_price_and_clears_cart(): void
    {
        $user = User::factory()->create();
        $product = $this->product('10.25');
        app(CartService::class)->put($user, $product, 2);

        $order = app(OrderService::class)->checkout($user);
        $item = $order->items->sole();

        $this->assertSame('20.50', $order->total_price);
        $this->assertSame('10.25', $item->price);
        $this->assertSame(2, $item->count);
        $this->assertSame($product->id, $order->products()->sole()->id);
        $this->assertSame(0, $user->products()->count());

        $paidOrder = app(OrderService::class)->markPaid($order);
        $this->assertSame(OrderStatus::PAID, $paidOrder->status);
    }

    private function product(string $price): Product
    {
        $category = Category::create(['url' => 'sushi-rolls', 'title' => 'Sushi rolls']);

        return Product::create([
            'title' => 'California roll',
            'price' => $price,
            'category_id' => $category->id,
        ]);
    }
}
