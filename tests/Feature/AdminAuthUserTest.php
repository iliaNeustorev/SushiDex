<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class AdminAuthUserTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_sign_in_as_another_user(): void
    {
        $target = User::factory()->create();

        $this->post(route('admin.users.auth-user', $target))
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }

    public function test_user_without_developer_access_cannot_switch_accounts(): void
    {
        $actor = User::factory()->create();
        $target = User::factory()->create();

        $this->actingAs($actor)
            ->post(route('admin.users.auth-user', $target))
            ->assertForbidden();

        $this->assertAuthenticatedAs($actor);
    }

    public function test_developer_can_switch_accounts_and_clear_previous_session_data(): void
    {
        Gate::define('dev', fn (User $user): bool => true);
        $actor = User::factory()->create();
        $target = User::factory()->create();

        $this->actingAs($actor)
            ->withSession(['private_marker' => 'remove-me'])
            ->post(route('admin.users.auth-user', $target))
            ->assertRedirect(route('home'))
            ->assertSessionMissing('private_marker');

        $this->assertAuthenticatedAs($target);
    }

    public function test_switching_to_self_preserves_the_session(): void
    {
        Gate::define('dev', fn (User $user): bool => true);
        $actor = User::factory()->create();

        $this->actingAs($actor)
            ->withSession(['private_marker' => 'keep-me'])
            ->from(route('admin.users.index'))
            ->post(route('admin.users.auth-user', $actor))
            ->assertRedirect(route('admin.users.index'))
            ->assertSessionHas('private_marker', 'keep-me');

        $this->assertAuthenticatedAs($actor);
    }

    public function test_deleted_user_cannot_be_used_as_a_target(): void
    {
        Gate::define('dev', fn (User $user): bool => true);
        $actor = User::factory()->create();
        $target = User::factory()->create();
        $target->delete();

        $this->actingAs($actor)
            ->post(route('admin.users.auth-user', $target->id))
            ->assertNotFound();

        $this->assertAuthenticatedAs($actor);
    }
}
