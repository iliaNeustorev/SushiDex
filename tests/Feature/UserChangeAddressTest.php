<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class UserChangeAddressTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_change_their_own_address(): void
    {
        $user = User::factory()->create(['address' => null]);

        $this->actingAs($user)
            ->patch(route('users.change-address', $user), ['address' => 'Москва, Тверская, 1'])
            ->assertRedirect();

        $this->assertSame('Москва, Тверская, 1', $user->fresh()->address);
    }

    public function test_user_cannot_change_another_users_address(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $this->actingAs($user)
            ->patch(route('users.change-address', $otherUser), ['address' => 'Новый адрес'])
            ->assertForbidden();

        $this->assertNotSame('Новый адрес', $otherUser->fresh()->address);
    }

    public function test_moderator_can_change_a_users_address(): void
    {
        Gate::define('moderator', fn (User $user): bool => true);
        $moderator = User::factory()->create();
        $user = User::factory()->create();

        $this->actingAs($moderator)
            ->patch(route('users.change-address', $user), ['address' => 'Адрес администратора'])
            ->assertRedirect();

        $this->assertSame('Адрес администратора', $user->fresh()->address);
    }

    public function test_address_is_required(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->patch(route('users.change-address', $user), ['address' => ''])
            ->assertSessionHasErrors('address');
    }
}
