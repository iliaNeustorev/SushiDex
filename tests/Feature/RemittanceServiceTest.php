<?php

namespace Tests\Feature;

use App\Enums\Orders\Status as OrderStatus;
use App\Enums\Orders\TypePaid;
use App\Enums\Remittances\Status as RemittanceStatus;
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
        $service = Mockery::mock(RemittanceService::class)->makePartial();
        $service->shouldReceive('getPaymentUrl')
            ->once()
            ->with(Mockery::type(Remittance::class))
            ->andReturn('https://payments.example/confirmation');

        $this->actingAs($user);

        $this->assertSame('https://payments.example/confirmation', $service->store($order));
        $this->assertDatabaseHas('remittances', [
            'order_id' => $order->id,
            'amount' => '10.25',
        ]);
    }

    public function test_user_cannot_request_payment_for_another_users_order(): void
    {
        $order = $this->order(User::factory()->create(), TypePaid::CARD_ONLINE);
        $this->actingAs(User::factory()->create());

        $this->expectException(AuthorizationException::class);

        app(RemittanceService::class)->store($order);
    }

    public function test_non_online_order_cannot_be_paid_online(): void
    {
        $user = User::factory()->create();
        $order = $this->order($user, TypePaid::IN_PICKUP_LOCATION);
        $this->actingAs($user);

        $this->expectException(ValidationException::class);

        app(RemittanceService::class)->store($order);
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

        app(RemittanceService::class)->store($order);
    }

    public function test_unconfigured_payment_gateway_fails_with_validation_error(): void
    {
        $remittance = new Remittance;

        try {
            app(RemittanceService::class)->getPaymentUrl($remittance);
            $this->fail('The payment gateway returned an URL without configuration.');
        } catch (ValidationException $exception) {
            $this->assertSame(
                ['Онлайн-оплата временно недоступна.'],
                $exception->errors()['payment'],
            );
        }
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
