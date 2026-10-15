<?php

namespace Tests\Feature;

use App\Enums\Orders\Status as OrderStatus;
use App\Enums\Orders\TypePaid;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class DashboardControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_shows_the_number_of_actual_orders(): void
    {
        Gate::define('moderator', fn (User $user): bool => true);
        $moderator = User::factory()->create();

        $this->order(OrderStatus::NEW);
        $this->order(OrderStatus::PROCESSING);
        $this->order(OrderStatus::COMPLETED);
        $this->order(OrderStatus::CANCELLED);

        $this->actingAs($moderator)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Admin/Dashboard/Index')
                ->where('countActualOrder', 2));
    }

    private function order(OrderStatus $status): Order
    {
        return Order::create([
            'user_id' => User::factory()->create()->id,
            'total_price' => '10.25',
            'status' => $status,
            'type_paid' => TypePaid::IN_PICKUP_LOCATION,
            'need_delivery' => false,
        ]);
    }
}
