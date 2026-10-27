<?php

namespace Tests\Feature;

use App\Enums\Orders\Status;
use App\Enums\Orders\TypePaid;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Str;
use Tests\TestCase;

class ProfileDeletionTest extends TestCase
{
    use DatabaseMigrations;

    public function test_guest_cannot_delete_a_profile(): void
    {
        $this->delete(route('profile.delete-profile'))
            ->assertRedirect(route('login'));
    }

    public function test_profile_is_soft_deleted_and_session_is_invalidated(): void
    {
        $user = User::factory()->create();
        $user->notifications()->create([
            'id' => (string) Str::uuid(),
            'type' => 'profile-test',
            'data' => ['text' => 'Test notification'],
        ]);

        $this->actingAs($user)
            ->withSession(['private_marker' => 'remove-me'])
            ->from(route('profile.index'))
            ->delete(route('profile.delete-profile'))
            ->assertRedirect(route('login'))
            ->assertInertiaFlash('type', 'info')
            ->assertSessionMissing('private_marker');

        $this->assertSoftDeleted($user);
        $this->assertGuest();
        $this->assertDatabaseMissing('notifications', ['notifiable_id' => $user->id]);
    }

    public function test_active_orders_prevent_profile_deletion(): void
    {
        foreach ([Status::NEW, Status::PROCESSING] as $status) {
            $user = User::factory()->create();
            $this->order($user, $status);

            $this->actingAs($user)
                ->from(route('profile.index'))
                ->delete(route('profile.delete-profile'))
                ->assertRedirect(route('profile.index'));

            $this->assertNotSoftDeleted($user);
            $this->assertAuthenticatedAs($user);
        }
    }

    public function test_completed_orders_are_preserved_when_profile_is_deleted(): void
    {
        $user = User::factory()->create();
        $order = $this->order($user, Status::COMPLETED);

        $this->actingAs($user)
            ->delete(route('profile.delete-profile'))
            ->assertRedirect(route('login'));

        $this->assertSoftDeleted($user);
        $this->assertDatabaseHas('orders', ['id' => $order->id, 'user_id' => $user->id]);
    }

    private function order(User $user, Status $status): Order
    {
        return Order::create([
            'user_id' => $user->id,
            'total_price' => '10.00',
            'status' => $status,
            'type_paid' => TypePaid::IN_PICKUP_LOCATION,
            'need_delivery' => false,
        ]);
    }
}
