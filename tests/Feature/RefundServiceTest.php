<?php

namespace Tests\Feature;

use App\DTO\Remittances\RemittanceRefundDTO;
use App\Enums\Orders\Status as OrderStatus;
use App\Enums\Orders\TypePaid;
use App\Enums\Refunds\Status as RefundStatus;
use App\Enums\Remittances\Status as RemittanceStatus;
use App\Interfaces\PaymentProviderInterface;
use App\Models\Order;
use App\Models\Refund;
use App\Models\Remittance;
use App\Models\User;
use App\Services\Refund\RefundService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Mockery;
use Tests\TestCase;

class RefundServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_succeeded_refund_updates_refund_and_remittance_immediately(): void
    {
        [$order, $remittance] = $this->paidOrder();
        $provider = $this->providerReturning('succeeded');

        (new RefundService($provider))->store($order);

        $refund = $remittance->refund()->firstOrFail();

        $this->assertSame(RefundStatus::SUCCEED, $refund->status);
        $this->assertSame('succeeded', $refund->external_system_status);
        $this->assertSame(self::REFUND_SYSTEM_ID, $refund->external_system_id);
        $this->assertNotNull($refund->refunded_at);
        $this->assertSame(RemittanceStatus::REFUND, $remittance->fresh()->status);
    }

    public function test_pending_refund_remains_new_until_webhook_arrives(): void
    {
        [$order, $remittance] = $this->paidOrder();
        $provider = $this->providerReturning('pending');

        (new RefundService($provider))->store($order);

        $refund = $remittance->refund()->firstOrFail();

        $this->assertSame(RefundStatus::NEW, $refund->status);
        $this->assertSame('pending', $refund->external_system_status);
        $this->assertSame(RemittanceStatus::PAID, $remittance->fresh()->status);
    }

    public function test_canceled_refund_is_recorded_and_reported_as_validation_error(): void
    {
        [$order, $remittance] = $this->paidOrder();
        $provider = $this->providerReturning('canceled');

        try {
            (new RefundService($provider))->store($order);
            $this->fail('A canceled refund was treated as successful.');
        } catch (ValidationException $exception) {
            $this->assertSame(
                ['Платёжная система отклонила возврат.'],
                $exception->errors()['refund'],
            );
        }

        $refund = $remittance->refund()->firstOrFail();

        $this->assertSame(RefundStatus::CANCELED, $refund->status);
        $this->assertSame('canceled', $refund->external_system_status);
        $this->assertSame(RemittanceStatus::PAID, $remittance->fresh()->status);
    }

    public function test_succeeded_refund_is_not_sent_to_provider_twice(): void
    {
        [$order, $remittance] = $this->paidOrder();
        $provider = $this->providerReturning('succeeded');
        $service = new RefundService($provider);

        $service->store($order);
        $service->store($order);

        $this->assertSame(1, $remittance->refund()->count());
    }

    private function providerReturning(string $status): PaymentProviderInterface
    {
        $provider = Mockery::mock(PaymentProviderInterface::class);
        $provider->shouldReceive('refund')
            ->once()
            ->with(Mockery::type(Remittance::class), Mockery::type(Refund::class))
            ->andReturn(new RemittanceRefundDTO(
                refundId: self::REFUND_SYSTEM_ID,
                status: $status,
                reason: 'Возврат средств за заказ',
                amount: '10.25',
            ));

        return $provider;
    }

    /**
     * @return array{Order, Remittance}
     */
    private function paidOrder(): array
    {
        $user = User::factory()->create();
        $order = Order::create([
            'user_id' => $user->id,
            'total_price' => '10.25',
            'status' => OrderStatus::NEW,
            'type_paid' => TypePaid::CARD_ONLINE,
            'need_delivery' => false,
        ]);
        $remittance = $order->remittances()->create([
            'amount' => '10.25',
            'currency' => 'RUB',
            'paid' => true,
            'status' => RemittanceStatus::PAID,
            'payment_system_id' => self::PAYMENT_SYSTEM_ID,
            'payment_system_status' => 'succeeded',
        ]);

        return [$order, $remittance];
    }

    private const PAYMENT_SYSTEM_ID = '22d6d597-000f-5000-9000-145f6df21d6f';

    private const REFUND_SYSTEM_ID = '216749f7-0016-50be-b000-078d43a63ae4';
}
