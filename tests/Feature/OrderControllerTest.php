<?php

namespace Tests\Feature;

use App\Enums\Orders\Status as OrderStatus;
use App\Enums\Orders\TypePaid;
use App\Enums\Remittances\Status as RemittanceStatus;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Services\Cart\CartService;
use Carbon\Carbon;
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

    public function test_actual_orders_filter_rejects_an_unknown_order_id(): void
    {
        Gate::define('moderator', fn (User $user): bool => true);
        $moderator = User::factory()->create();

        $this->actingAs($moderator)
            ->from(route('admin.orders.actual'))
            ->get(route('admin.orders.actual', ['filter' => ['id' => 999999]]))
            ->assertRedirect(route('admin.orders.actual'))
            ->assertSessionHasErrors([
                'filter.id' => 'Неправильный номер заказа',
            ]);
    }

    public function test_moderator_can_view_completed_orders_without_user_phone(): void
    {
        Gate::define('moderator', fn (User $user): bool => true);
        $moderator = User::factory()->create();
        $completedOrder = $this->order(User::factory()->create(), OrderStatus::COMPLETED);
        $completedOrder->update(['completed_at' => Carbon::parse('2026-10-07 12:00:00')]);
        $this->order(User::factory()->create());

        $this->actingAs($moderator)
            ->get(route('admin.orders.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Admin/Orders/Index')
                ->has('orders.data', 1)
                ->where('orders.data.0.id', $completedOrder->id)
                ->where('orders.data.0.completed_at', '2026-10-07T12:00:00+03:00')
                ->missing('orders.data.0.user.phone'));
    }

    public function test_moderator_can_filter_and_sort_completed_orders(): void
    {
        Gate::define('moderator', fn (User $user): bool => true);
        $moderator = User::factory()->create();
        $matchingOrder = $this->order(
            User::factory()->create(),
            OrderStatus::COMPLETED,
            TypePaid::CARD_ONLINE,
        );
        $matchingOrder->update([
            'created_at' => Carbon::parse('2026-10-01 10:00:00'),
            'completed_at' => Carbon::parse('2026-10-07 12:00:00'),
        ]);
        $matchingOrder->remittance()->create([
            'amount' => $matchingOrder->total_price,
            'status' => RemittanceStatus::PAID,
            'paid' => true,
        ]);
        $otherOrder = $this->order(
            User::factory()->create(),
            OrderStatus::COMPLETED,
            TypePaid::CARD_ONLINE,
        );
        $otherOrder->update([
            'created_at' => Carbon::parse('2026-09-20 10:00:00'),
            'completed_at' => Carbon::parse('2026-10-06 12:00:00'),
        ]);
        $this->order(User::factory()->create(), OrderStatus::CANCELLED);

        $this->actingAs($moderator)
            ->get(route('admin.orders.index', [
                'filter' => [
                    'status' => OrderStatus::COMPLETED->value,
                    'type_paid' => TypePaid::CARD_ONLINE->value,
                    'date_created_from' => '2026-10-01',
                    'date_created_to' => '2026-10-01',
                    'date_completed_from' => '2026-10-07',
                    'date_completed_to' => '2026-10-07',
                ],
                'sort' => 'completed_at',
            ]))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->has('orders.data', 1)
                ->where('orders.data.0.id', $matchingOrder->id)
                ->where('orders.data.0.remittance.status_text', RemittanceStatus::PAID->text()));
    }

    public function test_completed_orders_page_redirects_to_the_last_available_page(): void
    {
        Gate::define('moderator', fn (User $user): bool => true);
        $moderator = User::factory()->create();
        $this->order(User::factory()->create(), OrderStatus::COMPLETED);

        $this->actingAs($moderator)
            ->get(route('admin.orders.index', ['page' => 2, 'batch' => 10]))
            ->assertRedirect(route('admin.orders.index', ['page' => 1, 'batch' => 10]));
    }

    public function test_completed_orders_filter_rejects_an_unknown_order_id(): void
    {
        Gate::define('moderator', fn (User $user): bool => true);
        $moderator = User::factory()->create();

        $this->actingAs($moderator)
            ->from(route('admin.orders.index'))
            ->get(route('admin.orders.index', ['filter' => ['id' => 999999]]))
            ->assertRedirect(route('admin.orders.index'))
            ->assertSessionHasErrors([
                'filter.id' => 'Неправильный номер заказа',
            ]);
    }

    private function order(
        User $user,
        OrderStatus $status = OrderStatus::NEW,
        TypePaid $typePaid = TypePaid::IN_PICKUP_LOCATION,
    ): Order {
        return Order::create([
            'user_id' => $user->id,
            'total_price' => '10.25',
            'status' => $status,
            'type_paid' => $typePaid,
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
