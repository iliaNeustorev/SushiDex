<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PhoneControllerDestroyTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_delete_their_own_pending_phone(): void
    {
        $client = User::factory()->create();
        $pendingPhone = $client->pendingPhones()->create(['phone' => '79991234567']);
        $anotherPhone = $client->pendingPhones()->create(['phone' => '79997654321']);

        $this->actingAs($client)
            ->from(route('profile.index'))
            ->delete(route('profile.phone-destroy', $pendingPhone))
            ->assertRedirect(route('profile.index'));

        $this->assertDatabaseMissing('pending_phones', ['id' => $pendingPhone->id]);
        $this->assertDatabaseHas('pending_phones', ['id' => $anotherPhone->id]);
    }

    public function test_user_cannot_delete_another_users_pending_phone(): void
    {
        $client = User::factory()->create();
        $anotherClient = User::factory()->create();
        $pendingPhone = $anotherClient->pendingPhones()->create(['phone' => '79991234567']);

        $this->actingAs($client)
            ->delete(route('profile.phone-destroy', $pendingPhone))
            ->assertForbidden();

        $this->assertDatabaseHas('pending_phones', [
            'id' => $pendingPhone->id,
            'user_id' => $anotherClient->id,
        ]);
    }

    public function test_deleting_nonexistent_pending_phone_returns_not_found(): void
    {
        $client = User::factory()->create();

        $this->actingAs($client)
            ->delete(route('profile.phone-destroy', 999999))
            ->assertNotFound();
    }

    public function test_guest_cannot_delete_pending_phone(): void
    {
        $client = User::factory()->create();
        $pendingPhone = $client->pendingPhones()->create(['phone' => '79991234567']);

        $this->delete(route('profile.phone-destroy', $pendingPhone))
            ->assertRedirect(route('login'));

        $this->assertDatabaseHas('pending_phones', ['id' => $pendingPhone->id]);
    }
}
