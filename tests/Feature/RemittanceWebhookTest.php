<?php

namespace Tests\Feature;

use App\DTO\Remittances\RemittanceWebhookDTO;
use App\Enums\Orders\Status as OrderStatus;
use App\Enums\Orders\TypePaid;
use App\Enums\Refunds\Status as RefundStatus;
use App\Enums\Remittances\Status as RemittanceStatus;
use App\Integrations\PaymentProvider\YKassa;
use App\Integrations\PaymentProvider\YKassaAdapter;
use App\Models\Order;
use App\Models\Remittance;
use App\Models\User;
use App\Services\Remittance\RemittanceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;
use YooKassa\Model\Notification\NotificationEventType;

class RemittanceWebhookTest extends TestCase
{
    use RefreshDatabase;

    #[DataProvider('paymentEventProvider')]
    public function test_adapter_maps_yookassa_payment_events_to_internal_statuses(
        string $event,
        string $expectedStatus,
    ): void {
        $result = $this->adapter()->handleWebhook($this->payload($event));

        $this->assertSame(self::PAYMENT_SYSTEM_ID, $result->paymentSystemId);
        $this->assertSame($expectedStatus, $result->status);
    }

    public function test_succeeded_webhook_marks_remittance_as_paid(): void
    {
        $remittance = $this->remittance();

        $this->service()->handleWebhook(
            $this->payload(NotificationEventType::PAYMENT_SUCCEEDED),
        );

        $remittance->refresh();

        $this->assertTrue($remittance->paid);
        $this->assertSame(RemittanceStatus::PAID, $remittance->status);
        $this->assertSame('succeeded', $remittance->payment_system_status);
        $this->assertNotNull($remittance->payed_at);
    }

    public function test_repeated_succeeded_webhook_is_idempotent(): void
    {
        $remittance = $this->remittance();
        $payload = $this->payload(NotificationEventType::PAYMENT_SUCCEEDED);

        $this->travelTo('2026-10-13 10:00:00');
        $this->service()->handleWebhook($payload);
        $updatedAt = $remittance->fresh()->updated_at;

        $this->travelTo('2026-10-13 11:00:00');
        $this->service()->handleWebhook($payload);

        $this->assertTrue($updatedAt->equalTo($remittance->fresh()->updated_at));
    }

    #[DataProvider('pendingPaymentEventProvider')]
    public function test_non_final_webhook_updates_only_payment_system_status(
        string $event,
        string $expectedStatus,
    ): void {
        $remittance = $this->remittance();

        $this->service()->handleWebhook($this->payload($event));

        $remittance->refresh();

        $this->assertFalse($remittance->paid);
        $this->assertSame(RemittanceStatus::AWAIT_PAID, $remittance->status);
        $this->assertSame($expectedStatus, $remittance->payment_system_status);
        $this->assertNull($remittance->payed_at);
    }

    public function test_adapter_maps_refund_succeeded_event(): void
    {
        $result = $this->adapter()->handleWebhook($this->refundPayload());

        $this->assertSame(self::PAYMENT_SYSTEM_ID, $result->paymentSystemId);
        $this->assertSame(self::REFUND_SYSTEM_ID, $result->refundSystemId);
        $this->assertSame(RemittanceWebhookDTO::STATUS_REFUND, $result->status);
        $this->assertSame('succeeded', $result->externalStatus);
    }

    public function test_refund_succeeded_webhook_updates_refund_and_remittance(): void
    {
        $remittance = $this->paidRemittanceWithRefund();
        $refund = $remittance->refund;

        $this->service()->handleWebhook($this->refundPayload());

        $refund->refresh();
        $remittance->refresh();

        $this->assertSame(RefundStatus::SUCCEED, $refund->status);
        $this->assertSame('succeeded', $refund->external_system_status);
        $this->assertNotNull($refund->refunded_at);
        $this->assertSame(RemittanceStatus::REFUND, $remittance->status);
    }

    public function test_repeated_refund_succeeded_webhook_is_idempotent(): void
    {
        $remittance = $this->paidRemittanceWithRefund();
        $refund = $remittance->refund;

        $this->travelTo('2026-10-13 10:00:00');
        $this->service()->handleWebhook($this->refundPayload());
        $updatedAt = $refund->fresh()->updated_at;

        $this->travelTo('2026-10-13 11:00:00');
        $this->service()->handleWebhook($this->refundPayload());

        $this->assertTrue($updatedAt->equalTo($refund->fresh()->updated_at));
    }

    public static function paymentEventProvider(): array
    {
        return [
            'succeeded' => [
                NotificationEventType::PAYMENT_SUCCEEDED,
                RemittanceWebhookDTO::STATUS_SUCCEEDED,
            ],
            'waiting for capture' => [
                NotificationEventType::PAYMENT_WAITING_FOR_CAPTURE,
                RemittanceWebhookDTO::STATUS_WAITING,
            ],
            'canceled' => [
                NotificationEventType::PAYMENT_CANCELED,
                RemittanceWebhookDTO::STATUS_CANCELED,
            ],
        ];
    }

    public static function pendingPaymentEventProvider(): array
    {
        return [
            'waiting for capture' => [
                NotificationEventType::PAYMENT_WAITING_FOR_CAPTURE,
                'waiting_for_capture',
            ],
            'canceled' => [
                NotificationEventType::PAYMENT_CANCELED,
                RemittanceWebhookDTO::STATUS_CANCELED,
            ],
        ];
    }

    private const PAYMENT_SYSTEM_ID = '22d6d597-000f-5000-9000-145f6df21d6f';

    private const REFUND_SYSTEM_ID = '216749f7-0016-50be-b000-078d43a63ae4';

    private function adapter(): YKassaAdapter
    {
        return new YKassaAdapter(Mockery::mock(YKassa::class));
    }

    private function service(): RemittanceService
    {
        return new RemittanceService($this->adapter());
    }

    private function remittance(): Remittance
    {
        $user = User::factory()->create();
        $order = Order::create([
            'user_id' => $user->id,
            'total_price' => '10.25',
            'status' => OrderStatus::NEW,
            'type_paid' => TypePaid::CARD_ONLINE,
            'need_delivery' => false,
        ]);

        return $order->remittances()->create([
            'amount' => '10.25',
            'currency' => 'RUB',
            'status' => RemittanceStatus::AWAIT_PAID,
            'payment_system_id' => self::PAYMENT_SYSTEM_ID,
            'payment_system_status' => 'pending',
        ]);
    }

    private function paidRemittanceWithRefund(): Remittance
    {
        $remittance = $this->remittance();
        $remittance->update([
            'paid' => true,
            'status' => RemittanceStatus::PAID,
        ]);
        $remittance->refund()->create([
            'amount' => $remittance->amount,
            'external_system_id' => self::REFUND_SYSTEM_ID,
        ]);

        return $remittance->load('refund');
    }

    private function payload(string $event): array
    {
        return [
            'type' => 'notification',
            'event' => $event,
            'object' => [
                'id' => self::PAYMENT_SYSTEM_ID,
                'status' => str_replace('payment.', '', $event),
                'paid' => $event === NotificationEventType::PAYMENT_SUCCEEDED,
                'amount' => [
                    'value' => '10.25',
                    'currency' => 'RUB',
                ],
                'recipient' => [
                    'account_id' => '100500',
                    'gateway_id' => '100700',
                ],
                'created_at' => '2026-10-13T10:00:00.000Z',
                'test' => true,
            ],
        ];
    }

    private function refundPayload(): array
    {
        return [
            'type' => 'notification',
            'event' => NotificationEventType::REFUND_SUCCEEDED,
            'object' => [
                'id' => self::REFUND_SYSTEM_ID,
                'payment_id' => self::PAYMENT_SYSTEM_ID,
                'status' => 'succeeded',
                'amount' => [
                    'value' => '10.25',
                    'currency' => 'RUB',
                ],
                'created_at' => '2026-10-13T10:00:00.000Z',
            ],
        ];
    }
}
