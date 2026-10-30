<?php

namespace Tests\Feature;

use App\Integrations\DataParser\DaDataAdapter;
use App\Interfaces\DataParserInterface;
use Dadata\DadataClient;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Exceptions;
use Mockery;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use RuntimeException;
use Tests\TestCase;

class DaDataAdapterTest extends TestCase
{
    public function test_container_resolves_the_dadata_adapter_without_reading_credentials(): void
    {
        config(['dadata.token' => null, 'dadata.secret_key' => null]);

        $this->assertInstanceOf(DaDataAdapter::class, app(DataParserInterface::class));
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    #[DataProvider('providerResponses')]
    public function test_provider_results_are_mapped_to_address_strings(array $response, array $expected): void
    {
        config(['dadata.token' => 'token', 'dadata.secret_key' => 'secret']);
        $client = Mockery::mock('overload:'.DadataClient::class);
        $client->shouldReceive('__construct')->once()->with('token', 'secret');
        $client->shouldReceive('suggest')->once()->with('address', 'Москва')->andReturn($response);

        $this->assertSame($expected, app(DataParserInterface::class)->autoCompleteAddress('Москва')->all());
    }

    public static function providerResponses(): array
    {
        return [
            'matches' => [
                [
                    ['value' => 'г Москва, ул Тверская', 'data' => ['city' => 'Москва']],
                    ['value' => 'г Москва, ул Арбат', 'data' => ['city' => 'Москва']],
                ],
                ['г Москва, ул Тверская', 'г Москва, ул Арбат'],
            ],
            'no matches' => [[], []],
        ];
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function test_network_failure_is_reported_and_preserved_as_the_previous_exception(): void
    {
        config(['dadata.token' => 'token', 'dadata.secret_key' => 'secret']);
        Exceptions::fake();
        $failure = new ConnectException('Connection failed', new Request('POST', 'https://suggestions.dadata.ru'));
        $client = Mockery::mock('overload:'.DadataClient::class);
        $client->shouldReceive('suggest')->once()->with('address', 'Москва')->andThrow($failure);

        try {
            app(DataParserInterface::class)->autoCompleteAddress('Москва');
            $this->fail('A network failure must be wrapped.');
        } catch (RuntimeException $exception) {
            $this->assertSame($failure, $exception->getPrevious());
            $this->assertSame(
                'Не удалось получить подсказки адреса. Попробуйте позже.',
                $exception->getMessage(),
            );
            Exceptions::assertReported(fn (ConnectException $reported): bool => $reported === $failure);
        }
    }

    #[DataProvider('invalidCredentials')]
    public function test_invalid_credentials_are_reported_and_wrapped_before_any_network_request(
        mixed $token,
        mixed $secret,
    ): void {
        config(['dadata.token' => $token, 'dadata.secret_key' => $secret]);
        Exceptions::fake();

        try {
            app(DataParserInterface::class)->autoCompleteAddress('Москва');
            $this->fail('Invalid credentials must prevent an API request.');
        } catch (RuntimeException $exception) {
            $this->assertSame(
                'Не удалось получить подсказки адреса. Попробуйте позже.',
                $exception->getMessage(),
            );
            $this->assertInstanceOf(RuntimeException::class, $exception->getPrevious());
            $this->assertSame(
                'DaData token and secret key cannot be empty',
                $exception->getPrevious()->getMessage(),
            );
            Exceptions::assertReported(fn (RuntimeException $reported): bool => $reported === $exception->getPrevious());
        }
    }

    public static function invalidCredentials(): array
    {
        return [
            'missing token' => [null, 'secret'],
            'empty token' => ['', 'secret'],
            'invalid token type' => [123, 'secret'],
            'missing secret' => ['token', null],
            'empty secret' => ['token', ''],
            'invalid secret type' => ['token', 123],
        ];
    }
}
