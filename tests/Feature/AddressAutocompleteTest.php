<?php

namespace Tests\Feature;

use App\Interfaces\DataParserInterface;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use RuntimeException;
use Tests\TestCase;

class AddressAutocompleteTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_request_address_suggestions(): void
    {
        $this->mock(DataParserInterface::class, function (MockInterface $mock): void {
            $mock->shouldNotReceive('autoCompleteAddress');
        });

        $this->postJson(route('profile.auto-complete-address'), ['partAddress' => 'Москва'])
            ->assertUnauthorized();
    }

    public function test_blocked_user_cannot_request_address_suggestions(): void
    {
        $this->mock(DataParserInterface::class, function (MockInterface $mock): void {
            $mock->shouldNotReceive('autoCompleteAddress');
        });

        $this->actingAs(User::factory()->create(['block' => true]))
            ->postJson(route('profile.auto-complete-address'), ['partAddress' => 'Москва'])
            ->assertForbidden();
    }

    public function test_user_receives_suggestions_without_changing_their_saved_address(): void
    {
        $user = User::factory()->create(['address' => 'Старый адрес']);
        $suggestions = ['г Москва, ул Тверская', 'г Москва, ул Тверская-Ямская 1-я'];

        $this->mock(DataParserInterface::class, function (MockInterface $mock) use ($suggestions): void {
            $mock->shouldReceive('autoCompleteAddress')
                ->once()
                ->with('Москва Твер')
                ->andReturn(collect($suggestions));
        });

        $this->actingAs($user)
            ->postJson(route('profile.auto-complete-address'), ['partAddress' => '  Москва Твер  '])
            ->assertOk()
            ->assertExactJson(['data' => $suggestions]);

        $this->assertSame('Старый адрес', $user->fresh()->address);
    }

    public function test_no_matches_returns_an_empty_json_array(): void
    {
        $this->mock(DataParserInterface::class, function (MockInterface $mock): void {
            $mock->shouldReceive('autoCompleteAddress')->once()->with('Нет адреса')->andReturn(collect());
        });

        $this->actingAs(User::factory()->create())
            ->postJson(route('profile.auto-complete-address'), ['partAddress' => 'Нет адреса'])
            ->assertOk()
            ->assertExactJson(['data' => []]);
    }

    #[DataProvider('invalidQueries')]
    public function test_invalid_query_is_rejected_before_calling_the_provider(array $payload): void
    {
        $this->mock(DataParserInterface::class, function (MockInterface $mock): void {
            $mock->shouldNotReceive('autoCompleteAddress');
        });

        $this->actingAs(User::factory()->create())
            ->postJson(route('profile.auto-complete-address'), $payload)
            ->assertUnprocessable()
            ->assertJsonValidationErrors('partAddress');
    }

    public static function invalidQueries(): array
    {
        return [
            'missing' => [[]],
            'null' => [['partAddress' => null]],
            'empty' => [['partAddress' => '']],
            'whitespace' => [['partAddress' => '   ']],
            'too short' => [['partAddress' => 'Мо']],
            'too long' => [['partAddress' => str_repeat('а', 256)]],
            'array' => [['partAddress' => ['Москва']]],
        ];
    }

    public function test_provider_failure_returns_a_safe_service_unavailable_response(): void
    {
        $this->mock(DataParserInterface::class, function (MockInterface $mock): void {
            $mock->shouldReceive('autoCompleteAddress')
                ->once()
                ->with('Москва')
                ->andThrow(new RuntimeException('Sensitive provider response'));
        });

        $this->actingAs(User::factory()->create())
            ->postJson(route('profile.auto-complete-address'), ['partAddress' => 'Москва'])
            ->assertStatus(503)
            ->assertExactJson(['message' => 'Не удалось получить подсказки адреса.']);
    }
}
