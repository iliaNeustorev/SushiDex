<?php

namespace Tests\Feature;

use App\Http\Controllers\Client\ProfileController;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProfileChangePasswordTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_change_a_password(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('current-password'),
        ]);

        $this->put($this->changePasswordUrl(), $this->validPayload())
            ->assertRedirect(route('login'));

        $this->assertTrue(Hash::check('current-password', $user->fresh()->password));
    }

    public function test_authenticated_user_can_change_their_password(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('current-password'),
            'remember_token' => 'old-remember-token',
        ]);

        $this->actingAs($user)
            ->from(route('profile.index'))
            ->put($this->changePasswordUrl(), $this->validPayload())
            ->assertRedirect(route('profile.index'))
            ->assertSessionHasNoErrors();

        $user->refresh();

        $this->assertTrue(Hash::check('new-password', $user->password));
        $this->assertFalse(Hash::check('current-password', $user->password));
        $this->assertNotSame('old-remember-token', $user->remember_token);
        $this->assertSame(60, strlen($user->remember_token));
    }

    public function test_password_is_not_changed_when_current_password_is_incorrect(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('current-password'),
            'remember_token' => 'old-remember-token',
        ]);

        $payload = $this->validPayload();
        $payload['current_password'] = 'incorrect-password';

        $this->actingAs($user)
            ->put($this->changePasswordUrl(), $payload)
            ->assertSessionHasErrors('current_password');

        $user->refresh();

        $this->assertTrue(Hash::check('current-password', $user->password));
        $this->assertSame('old-remember-token', $user->remember_token);
    }

    public function test_password_confirmation_must_match(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('current-password'),
        ]);

        $payload = $this->validPayload();
        $payload['password_confirmation'] = 'another-password';

        $this->actingAs($user)
            ->put($this->changePasswordUrl(), $payload)
            ->assertSessionHasErrors('password');

        $this->assertTrue(Hash::check('current-password', $user->fresh()->password));
    }

    public function test_new_password_must_have_at_least_eight_characters(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('current-password'),
        ]);

        $payload = $this->validPayload();
        $payload['password'] = 'short';
        $payload['password_confirmation'] = 'short';

        $this->actingAs($user)
            ->put($this->changePasswordUrl(), $payload)
            ->assertSessionHasErrors('password');

        $this->assertTrue(Hash::check('current-password', $user->fresh()->password));
    }

    /**
     * @return array{current_password: string, password: string, password_confirmation: string}
     */
    private function validPayload(): array
    {
        return [
            'current_password' => 'current-password',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ];
    }

    private function changePasswordUrl(): string
    {
        return action([ProfileController::class, 'changePassword']);
    }
}
