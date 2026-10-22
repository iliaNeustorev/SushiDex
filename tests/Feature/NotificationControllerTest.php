<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\Client\OrderCreated;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Str;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class NotificationControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_access_notifications(): void
    {
        $this->get(route('notifications.index'))->assertRedirect();
        $this->patch(route('notifications.update', Str::uuid()->toString()))->assertRedirect();
        $this->post(route('notifications.read-all'))->assertRedirect();
    }

    public function test_list_contains_only_own_unread_notifications_by_default(): void
    {
        $user = User::factory()->create();
        $notification = $this->notification($user);
        $this->notification($user, true);
        $this->notification(User::factory()->create());

        $this->actingAs($user)->get(route('notifications.index'))
            ->assertOk()->assertInertia(fn (Assert $page) => $page
            ->component('Notifications/Index')
            ->has('notifications', 1)
            ->where('notifications.0.id', $notification->id)
            ->where('notifications.0.read_at', null)
            ->where('notifications.0.data.sender', 'Система')
            ->where('checkUnreadNotify', true));
    }

    public function test_all_filter_includes_read_notifications_and_paginates(): void
    {
        $user = User::factory()->create();
        for ($i = 0; $i < 7; $i++) {
            $this->notification($user, true);
        }
        $this->notification(User::factory()->create());

        $this->actingAs($user)->get(route('notifications.index', ['all' => 1]))
            ->assertOk()->assertInertia(fn (Assert $page) => $page
            ->has('notifications', 5)->where('lastPage', 2)
            ->where('query.all', true)->where('checkUnreadNotify', false));

        $this->get(route('notifications.index', ['all' => 1, 'page' => 2]))
            ->assertOk()->assertInertia(fn (Assert $page) => $page
            ->has('notifications', 2)->where('page', 2));

        $this->get(route('notifications.index', ['all' => 0]))
            ->assertOk()->assertInertia(fn (Assert $page) => $page->has('notifications', 0));
    }

    public function test_invalid_filter_is_rejected(): void
    {
        $this->actingAs(User::factory()->create())
            ->getJson(route('notifications.index', ['all' => 'invalid']))
            ->assertUnprocessable()->assertJsonValidationErrors('all');
    }

    public function test_reading_is_idempotent_and_cannot_modify_another_users_notification(): void
    {
        $user = User::factory()->create();
        $own = $this->notification($user);
        $other = $this->notification(User::factory()->create());

        $this->actingAs($user)->patch(route('notifications.update', $other->id))->assertRedirect();
        $this->assertNull($other->fresh()->read_at);

        $this->patch(route('notifications.update', $own->id))->assertRedirect();
        $readAt = $own->fresh()->read_at->toISOString();
        $this->travel(1)->minutes();
        $this->patch(route('notifications.update', $own->id))->assertRedirect();
        $this->assertSame($readAt, $own->fresh()->read_at->toISOString());
    }

    public function test_read_all_updates_only_own_unread_notifications(): void
    {
        $user = User::factory()->create();
        $unread = $this->notification($user);
        $read = $this->notification($user, true);
        $readAt = $read->read_at->toISOString();
        $other = $this->notification(User::factory()->create());

        $this->actingAs($user)->post(route('notifications.read-all'))->assertRedirect();

        $this->assertNotNull($unread->fresh()->read_at);
        $this->assertSame($readAt, $read->fresh()->read_at->toISOString());
        $this->assertNull($other->fresh()->read_at);
    }

    private function notification(User $user, bool $read = false): DatabaseNotification
    {
        return $user->notifications()->create([
            'id' => Str::uuid()->toString(),
            'type' => OrderCreated::class,
            'data' => ['message' => 'Новый заказ', 'url' => '/orders/123', 'sender' => 'Система'],
            'read_at' => $read ? now()->subMinute() : null,
        ]);
    }
}
