<?php

namespace Tests\Feature;

use App\DTO\Remittances\RemittanceWebhookDTO;
use App\Interfaces\PaymentProviderInterface;
use App\Models\Order;
use App\Models\Refund;
use App\Models\Remittance;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Mocks\MockYkassaAdapter;
use Tests\TestCase;
use YooKassa\Model\Notification\NotificationEventType;

class MockYkassaAdapterTest extends TestCase
{
    public function test_testing_environment_uses_mock_payment_provider(): void
    {
        $this->assertInstanceOf(MockYkassaAdapter::class, app(PaymentProviderInterface::class));
    }

    public function test_store_returns_yookassa_like_pending_payment(): void
    {
        $remittance = new Remittance([
            'amount' => '1250.50',
            'currency' => 'RUB',
        ]);
        $remittance->id = 42;
        $order = new Order;
        $order->id = 72;

        $result = $this->adapter()->store($remittance, $order);

        $this->assertSame('23d93cac-000f-5000-8000-000000000042', $result->paymentId);
        $this->assertSame('pending', $result->status);
        $this->assertSame('1250.50', $result->amount);
        $this->assertSame(route('orders.show', $order), $result->url);
    }

    #[DataProvider('paymentEventProvider')]
    public function test_handle_webhook_maps_yookassa_payment_events(
        string $event,
        string $externalStatus,
        string $expectedStatus,
    ): void {
        $result = $this->adapter()->handleWebhook([
            'type' => 'notification',
            'event' => $event,
            'object' => [
                'id' => '23d93cac-000f-5000-8000-000000000042',
                'status' => $externalStatus,
                'paid' => $event === NotificationEventType::PAYMENT_SUCCEEDED,
                'amount' => [
                    'value' => '1250.50',
                    'currency' => 'RUB',
                ],
                'test' => true,
            ],
        ]);

        $this->assertSame('23d93cac-000f-5000-8000-000000000042', $result->paymentSystemId);
        $this->assertSame($expectedStatus, $result->status);
        $this->assertSame($externalStatus, $result->externalStatus);
        $this->assertNull($result->refundSystemId);
    }

    public function test_handle_webhook_maps_yookassa_refund_event(): void
    {
        $result = $this->adapter()->handleWebhook([
            'type' => 'notification',
            'event' => NotificationEventType::REFUND_SUCCEEDED,
            'object' => [
                'id' => '216749f7-0016-50be-b000-000000000007',
                'payment_id' => '23d93cac-000f-5000-8000-000000000042',
                'status' => 'succeeded',
                'amount' => [
                    'value' => '1250.50',
                    'currency' => 'RUB',
                ],
            ],
        ]);

        $this->assertSame('23d93cac-000f-5000-8000-000000000042', $result->paymentSystemId);
        $this->assertSame(RemittanceWebhookDTO::STATUS_REFUND, $result->status);
        $this->assertSame('succeeded', $result->externalStatus);
        $this->assertSame('216749f7-0016-50be-b000-000000000007', $result->refundSystemId);
    }

    public function test_refund_returns_yookassa_like_succeeded_refund(): void
    {
        $remittance = new Remittance(['amount' => '1250.50']);
        $refund = new Refund([
            'amount' => '1250.50',
            'reason' => 'Тестовый возврат',
        ]);
        $refund->id = 7;

        $result = $this->adapter()->refund($remittance, $refund);

        $this->assertSame('216749f7-0016-50be-b000-000000000007', $result->refundId);
        $this->assertSame('succeeded', $result->status);
        $this->assertSame('Тестовый возврат', $result->reason);
        $this->assertSame('1250.50', $result->amount);
    }

    public function test_handle_webhook_rejects_unknown_event(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->adapter()->handleWebhook([
            'type' => 'notification',
            'event' => 'payment.pending',
            'object' => [],
        ]);
    }

    public static function paymentEventProvider(): array
    {
        return [
            'succeeded' => [
                NotificationEventType::PAYMENT_SUCCEEDED,
                'succeeded',
                RemittanceWebhookDTO::STATUS_SUCCEEDED,
            ],
            'waiting for capture' => [
                NotificationEventType::PAYMENT_WAITING_FOR_CAPTURE,
                'waiting_for_capture',
                RemittanceWebhookDTO::STATUS_WAITING,
            ],
            'canceled' => [
                NotificationEventType::PAYMENT_CANCELED,
                'canceled',
                RemittanceWebhookDTO::STATUS_CANCELED,
            ],
        ];
    }

    private function adapter(): MockYkassaAdapter
    {
        return new MockYkassaAdapter;
    }
}
