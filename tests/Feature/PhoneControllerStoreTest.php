<?php

namespace Tests\Feature;

use App\Models\Phone;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PhoneControllerStoreTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_add_a_pending_phone(): void
    {
        $this->post(route('profile.phone-store'), ['phone' => '79991234567'])
            ->assertRedirect(route('login'));

        $this->assertDatabaseCount('pending_phones', 0);
    }

    public function test_authenticated_user_can_add_a_pending_phone(): void
    {
        $client = User::factory()->create();

        $this->actingAs($client)
            ->from(route('profile.index'))
            ->post(route('profile.phone-store'), ['phone' => '79991234567'])
            ->assertRedirect(route('profile.index'))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('pending_phones', [
            'user_id' => $client->id,
            'phone' => '79991234567',
        ]);
        $this->assertDatabaseCount('pending_phones', 1);
    }

    public function test_user_cannot_add_the_same_pending_phone_twice(): void
    {
        $client = User::factory()->create();
        $client->pendingPhones()->create(['phone' => '79991234567']);

        $this->actingAs($client)
            ->post(route('profile.phone-store'), ['phone' => '79991234567'])
            ->assertSessionHasErrors('phone');

        $this->assertDatabaseCount('pending_phones', 1);
    }

    public function test_another_users_pending_phone_can_be_added(): void
    {
        $client = User::factory()->create();
        $anotherClient = User::factory()->create();
        $anotherClient->pendingPhones()->create(['phone' => '79991234567']);

        $this->actingAs($client)
            ->post(route('profile.phone-store'), ['phone' => '79991234567'])
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('pending_phones', [
            'user_id' => $client->id,
            'phone' => '79991234567',
        ]);
        $this->assertDatabaseCount('pending_phones', 2);
    }

    public function test_confirmed_phone_cannot_be_added_as_pending(): void
    {
        $client = User::factory()->create();
        Phone::factory()->for(User::factory())->create(['phone' => '79991234567']);

        $this->actingAs($client)
            ->post(route('profile.phone-store'), ['phone' => '79991234567'])
            ->assertSessionHasErrors('phone');

        $this->assertDatabaseCount('pending_phones', 0);
    }

    public function test_invalid_phone_is_not_added(): void
    {
        $client = User::factory()->create();

        $this->actingAs($client)
            ->post(route('profile.phone-store'), ['phone' => '89991234567'])
            ->assertSessionHasErrors('phone');

        $this->assertDatabaseCount('pending_phones', 0);
    }
}
