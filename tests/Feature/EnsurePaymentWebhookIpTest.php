<?php

namespace Tests\Feature;

use App\Http\Middleware\EnsurePaymentWebhookIp;
use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class EnsurePaymentWebhookIpTest extends TestCase
{
    #[DataProvider('allowedIpProvider')]
    public function test_production_accepts_yookassa_ip_addresses(string $ip): void
    {
        $this->useProductionEnvironment();

        $response = app(EnsurePaymentWebhookIp::class)->handle(
            $this->requestFrom($ip),
            fn () => response('', 200),
        );

        $this->assertSame(200, $response->getStatusCode());
    }

    public function test_production_rejects_an_unknown_ip_address(): void
    {
        $this->useProductionEnvironment();

        try {
            app(EnsurePaymentWebhookIp::class)->handle(
                $this->requestFrom('203.0.113.10'),
                fn () => response('', 200),
            );

            $this->fail('An unknown webhook IP address was accepted.');
        } catch (HttpException $exception) {
            $this->assertSame(403, $exception->getStatusCode());
        }
    }

    public static function allowedIpProvider(): array
    {
        return [
            'IPv4 subnet' => ['185.71.76.1'],
            'single IPv4 address' => ['77.75.156.11'],
            'IPv6 subnet' => ['2a02:5180::1'],
        ];
    }

    private function useProductionEnvironment(): void
    {
        $this->app->detectEnvironment(fn () => 'production');
    }

    private function requestFrom(string $ip): Request
    {
        return Request::create(
            '/remittances/callbacks',
            'POST',
            server: ['REMOTE_ADDR' => $ip],
        );
    }
}
