<?php

namespace Tests\Feature;

use App\Enums\Orders\Status as OrderStatus;
use App\Enums\Orders\TypePaid;
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

    public function test_cart_item_is_created_and_its_quantity_is_replaced(): void
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

    public function test_setting_cart_item_quantity_to_zero_removes_it(): void
    {
        $user = User::factory()->create();
        $product = $this->product('10.25');
        $service = app(CartService::class);

        $service->put($user, $product, 2);
        $service->put($user, $product, 0);

        $this->assertDatabaseMissing('carts', [
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);
        $this->assertSame(0, $user->products()->count());
    }

    public function test_temporary_cart_is_merged_with_user_cart(): void
    {
        $user = User::factory()->create();
        $existingProduct = $this->product('10.25');
        $newProduct = $this->product('20.50');
        $productWithGreaterSavedCount = $this->product('5.00');
        $existingProduct->update(['active' => true]);
        $newProduct->update(['active' => true]);
        $productWithGreaterSavedCount->update(['active' => true]);
        $service = app(CartService::class);
        $service->put($user, $existingProduct, 2);
        $service->put($user, $productWithGreaterSavedCount, 4);

        $result = $service->syncTemp($user, [
            ['product_id' => $existingProduct->id, 'count' => 3],
            ['product_id' => $newProduct->id, 'count' => 2],
            ['product_id' => $productWithGreaterSavedCount->id, 'count' => 1],
        ]);

        $this->assertTrue($result['success']);
        $this->assertSame(3, $user->products()->count());
        $this->assertDatabaseHas('carts', [
            'user_id' => $user->id,
            'product_id' => $existingProduct->id,
            'count' => 3,
        ]);
        $this->assertDatabaseHas('carts', [
            'user_id' => $user->id,
            'product_id' => $newProduct->id,
            'count' => 2,
        ]);
        $this->assertDatabaseHas('carts', [
            'user_id' => $user->id,
            'product_id' => $productWithGreaterSavedCount->id,
            'count' => 4,
        ]);
        $this->assertCount(3, $result['items']);
        $this->assertSame(91.75, $result['total_price']);
    }

    public function test_create_calculates_total_snapshots_price_and_clears_cart(): void
    {
        $user = User::factory()->create();
        $product = $this->product('10.25');
        app(CartService::class)->put($user, $product, 2);

        $order = app(OrderService::class)->create($user, [
            'type' => TypePaid::IN_PICKUP_LOCATION->value,
            'need_delivery' => false,
        ]);
        $item = $order->items->sole();

        $this->assertSame('20.50', $order->total_price);
        $this->assertSame('10.25', $item->price);
        $this->assertSame(2, $item->count);
        $this->assertSame($product->id, $order->products()->sole()->id);
        $this->assertSame(0, $user->products()->count());
        $this->assertSame(OrderStatus::NEW, $order->status);
        $this->assertSame(TypePaid::IN_PICKUP_LOCATION, $order->type_paid);
        $this->assertFalse($order->need_delivery);
    }

    private function product(string $price): Product
    {
        $category = Category::firstOrCreate(
            ['url' => 'sushi-rolls'],
            ['title' => 'Sushi rolls'],
        );

        return Product::create([
            'title' => 'California roll',
            'price' => $price,
            'category_id' => $category->id,
        ]);
    }
}
