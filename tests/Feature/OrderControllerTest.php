<?php

namespace Tests\Feature;

use App\Enums\Orders\Status as OrderStatus;
use App\Enums\Orders\TypePaid;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Services\Cart\CartService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class OrderControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_sees_only_their_actual_orders(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $newOrder = $this->order($user);
        $this->order($user, OrderStatus::COMPLETED);
        $this->order($otherUser);

        $this->actingAs($user)
            ->get(route('orders.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Order/Index')
                ->has('orders', 1)
                ->where('orders.0.id', $newOrder->id)
                ->where('orders.0.items_count', 0));
    }

    public function test_user_can_view_their_order_but_not_another_users_order(): void
    {
        $user = User::factory()->create();
        $ownOrder = $this->order($user);
        $otherOrder = $this->order(User::factory()->create());

        $this->actingAs($user)
            ->get(route('orders.show', $ownOrder))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Order/Show')
                ->where('order.id', $ownOrder->id)
                ->where('order.items_count', 0));

        $this->actingAs($user)
            ->get(route('orders.show', $otherOrder))
            ->assertForbidden();
    }

    public function test_user_can_create_an_order_from_their_cart(): void
    {
        $user = User::factory()->create();
        app(CartService::class)->put($user, $this->product(), 2);

        $response = $this->actingAs($user)->post(route('orders.store'), [
            'type' => TypePaid::IN_PICKUP_LOCATION->value,
            'need_delivery' => false,
        ]);

        $order = $user->orders()->sole();
        $response->assertRedirect(route('orders.show', $order));
        $this->assertSame(0, $user->products()->count());
    }

    public function test_moderator_can_update_order_status(): void
    {
        Gate::define('moderator', fn (User $user): bool => true);
        $moderator = User::factory()->create();
        $order = $this->order(User::factory()->create());

        $this->actingAs($moderator)
            ->patch(route('admin.orders.update', $order), [
                'status' => OrderStatus::PROCESSING->value,
            ])
            ->assertRedirect();

        $this->assertSame(OrderStatus::PROCESSING, $order->fresh()->status);
    }

    public function test_order_owner_can_update_settings_but_another_user_cannot(): void
    {
        $owner = User::factory()->create();
        $order = $this->order($owner);

        $this->actingAs($owner)
            ->patch(route('orders.settings.update', $order), [
                'need_delivery' => true,
                'type' => TypePaid::CASH_COURIER->value,
            ])
            ->assertRedirect();

        $order->refresh();
        $this->assertTrue($order->need_delivery);
        $this->assertSame(TypePaid::CASH_COURIER, $order->type_paid);

        $this->actingAs(User::factory()->create())
            ->patch(route('orders.settings.update', $order), [
                'type' => TypePaid::CARD_ONLINE->value,
            ])
            ->assertForbidden();
    }

    public function test_moderator_can_filter_actual_orders_by_id(): void
    {
        Gate::define('moderator', fn (User $user): bool => true);
        $moderator = User::factory()->create();
        $matchingOrder = $this->order(User::factory()->create());
        $this->order(User::factory()->create());

        $this->actingAs($moderator)
            ->get(route('admin.orders.actual', ['filter' => ['id' => $matchingOrder->id]]))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Admin/Orders/Actual')
                ->has('actualOrders.data', 1)
                ->where('actualOrders.data.0.id', $matchingOrder->id));
    }

    private function order(User $user, OrderStatus $status = OrderStatus::NEW): Order
    {
        return Order::create([
            'user_id' => $user->id,
            'total_price' => '10.25',
            'status' => $status,
            'type_paid' => TypePaid::IN_PICKUP_LOCATION,
            'need_delivery' => false,
        ]);
    }

    private function product(): Product
    {
        $category = Category::create([
            'url' => 'sushi-rolls',
            'title' => 'Sushi rolls',
        ]);

        return Product::create([
            'title' => 'California roll',
            'price' => '10.25',
            'category_id' => $category->id,
            'active' => true,
        ]);
    }
}
