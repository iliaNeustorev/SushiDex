<?php

namespace Tests\Feature;

use App\Enums\Orders\Status as OrderStatus;
use App\Enums\Orders\TypePaid;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class ProfileOrderPaginationTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_contains_only_the_users_first_order_page_with_resource_fields(): void
    {
        $user = User::factory()->create();

        foreach (range(1, 6) as $index) {
            $this->order($user, $index === 6 ? OrderStatus::COMPLETED : OrderStatus::NEW);
        }

        $this->order(User::factory()->create());

        $this->actingAs($user)
            ->get(route('profile.index', ['tab' => 'orders']))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Profile/Index')
                ->has('orders', 5)
                ->where('orders.0.status', OrderStatus::COMPLETED->value)
                ->where('orders.0.status_text', OrderStatus::COMPLETED->text())
                ->where('orders.0.type_paid', TypePaid::IN_PICKUP_LOCATION->value)
                ->where('orders.0.type_paid_text', TypePaid::IN_PICKUP_LOCATION->text())
                ->where('orders.0.items_count', 0)
                ->where('page', 1)
                ->where('lastPage', 2));
    }

    public function test_profile_can_load_the_next_order_page(): void
    {
        $user = User::factory()->create();
        $orders = collect(range(1, 6))
            ->map(fn () => $this->order($user));

        $this->actingAs($user)
            ->get(route('profile.index', ['page' => 2, 'tab' => 'orders']))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->has('orders', 1)
                ->where('orders.0.id', $orders->first()->id)
                ->where('page', 2)
                ->where('lastPage', 2));
    }

    public function test_profile_redirects_an_out_of_range_order_page_to_the_first_page(): void
    {
        $user = User::factory()->create();
        $this->order($user);

        $this->actingAs($user)
            ->get(route('profile.index', ['page' => 2, 'tab' => 'orders']))
            ->assertRedirect(route('profile.index', [
                'page' => 1,
                'tab' => 'orders',
            ]));
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
}
