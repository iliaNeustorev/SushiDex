<?php

namespace Tests\Feature;

use App\DTO\Remittances\RemittancePaymentDTO;
use App\Enums\Orders\Status as OrderStatus;
use App\Enums\Orders\TypePaid;
use App\Enums\Remittances\Status as RemittanceStatus;
use App\Interfaces\PaymentProviderInterface;
use App\Models\Order;
use App\Models\Remittance;
use App\Models\User;
use App\Services\Remittance\RemittanceService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Mockery;
use Tests\TestCase;

class RemittanceServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_request_payment_url_for_an_online_order(): void
    {
        $user = User::factory()->create();
        $order = $this->order($user, TypePaid::CARD_ONLINE);
        $provider = Mockery::mock(PaymentProviderInterface::class);
        $provider->shouldReceive('store')
            ->once()
            ->with(Mockery::type(Remittance::class), $order)
            ->andReturn(new RemittancePaymentDTO(
                paymentId: 'payment-1',
                status: 'pending',
                amount: '10.25',
                url: 'https://payments.example/confirmation',
            ));
        $service = new RemittanceService($provider);

        $this->actingAs($user);

        $this->assertSame('https://payments.example/confirmation', $service->store($order));
        $this->assertDatabaseHas('remittances', [
            'order_id' => $order->id,
            'amount' => '10.25',
            'payment_system_id' => 'payment-1',
            'payment_system_status' => 'pending',
            'payment_system_amount' => '10.25',
        ]);
    }

    public function test_user_cannot_request_payment_for_another_users_order(): void
    {
        $order = $this->order(User::factory()->create(), TypePaid::CARD_ONLINE);
        $this->actingAs(User::factory()->create());

        $this->expectException(AuthorizationException::class);

        $this->service()->store($order);
    }

    public function test_non_online_order_cannot_be_paid_online(): void
    {
        $user = User::factory()->create();
        $order = $this->order($user, TypePaid::IN_PICKUP_LOCATION);
        $this->actingAs($user);

        $this->expectException(ValidationException::class);

        $this->service()->store($order);
    }

    public function test_paid_remittance_cannot_request_another_payment_url(): void
    {
        $user = User::factory()->create();
        $order = $this->order($user, TypePaid::CARD_ONLINE);
        $order->remittance()->create([
            'amount' => $order->total_price,
            'paid' => true,
            'status' => RemittanceStatus::PAID,
        ]);
        $this->actingAs($user);

        $this->expectException(ValidationException::class);

        $this->service()->store($order);
    }

    public function test_refunded_remittance_cannot_request_another_payment_url(): void
    {
        $user = User::factory()->create();
        $order = $this->order($user, TypePaid::CARD_ONLINE);
        $order->remittances()->create([
            'amount' => $order->total_price,
            'paid' => true,
            'status' => RemittanceStatus::REFUND,
        ]);
        $this->actingAs($user);

        $this->expectException(ValidationException::class);

        $this->service()->store($order);
    }

    public function test_canceled_order_cannot_request_payment_url(): void
    {
        $user = User::factory()->create();
        $order = $this->order($user, TypePaid::CARD_ONLINE);
        $order->update(['status' => OrderStatus::CANCELLED]);
        $this->actingAs($user);

        $this->expectException(ValidationException::class);

        $this->service()->store($order);
    }

    public function test_completed_order_cannot_request_payment_url(): void
    {
        $user = User::factory()->create();
        $order = $this->order($user, TypePaid::CARD_ONLINE);
        $order->update(['status' => OrderStatus::COMPLETED]);
        $this->actingAs($user);

        $this->expectException(ValidationException::class);

        $this->service()->store($order);
    }

    public function test_processing_order_can_request_payment_url(): void
    {
        $user = User::factory()->create();
        $order = $this->order($user, TypePaid::CARD_ONLINE);
        $order->update(['status' => OrderStatus::PROCESSING]);
        $provider = Mockery::mock(PaymentProviderInterface::class);
        $provider->shouldReceive('store')
            ->once()
            ->andReturn(new RemittancePaymentDTO(
                paymentId: 'payment-processing',
                status: 'pending',
                amount: '10.25',
                url: 'https://payments.example/processing',
            ));
        $this->actingAs($user);

        $url = (new RemittanceService($provider))->store($order);

        $this->assertSame('https://payments.example/processing', $url);
    }

    public function test_payment_retry_reuses_an_unsubmitted_remittance(): void
    {
        $user = User::factory()->create();
        $order = $this->order($user, TypePaid::CARD_ONLINE);
        $provider = Mockery::mock(PaymentProviderInterface::class);
        $provider->shouldReceive('store')
            ->twice()
            ->andThrow(new \Exception('Provider error'));
        $service = new RemittanceService($provider);
        $this->actingAs($user);

        for ($attempt = 0; $attempt < 2; $attempt++) {
            try {
                $service->store($order);
            } catch (ValidationException) {
                // The same local remittance should be reused on the next attempt.
            }
        }

        $this->assertDatabaseCount('remittances', 1);
    }

    public function test_payment_provider_error_becomes_validation_error(): void
    {
        $user = User::factory()->create();
        $order = $this->order($user, TypePaid::CARD_ONLINE);
        $provider = Mockery::mock(PaymentProviderInterface::class);
        $provider->shouldReceive('store')
            ->once()
            ->andThrow(new \Exception('Provider error'));
        $this->actingAs($user);

        try {
            (new RemittanceService($provider))->store($order);
            $this->fail('The payment provider error was not converted to a validation error.');
        } catch (ValidationException $exception) {
            $this->assertSame(
                ['Во время оплаты произошла ошибка. Попробуйте еще раз'],
                $exception->errors()['payment'],
            );
        }
    }

    private function service(): RemittanceService
    {
        return new RemittanceService(Mockery::mock(PaymentProviderInterface::class));
    }

    private function order(User $user, TypePaid $type): Order
    {
        return Order::create([
            'user_id' => $user->id,
            'total_price' => '10.25',
            'status' => OrderStatus::NEW,
            'type_paid' => $type,
            'need_delivery' => false,
        ]);
    }
}
