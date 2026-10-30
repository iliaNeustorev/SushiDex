<?php

namespace Tests\Feature;

use App\DTO\Remittances\RemittanceRefundDTO;
use App\Enums\Orders\Status as OrderStatus;
use App\Enums\Orders\TypePaid;
use App\Enums\Refunds\Status as RefundStatus;
use App\Enums\Remittances\Status as RemittanceStatus;
use App\Interfaces\PaymentProviderInterface;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\Refund;
use App\Models\Remittance;
use App\Models\User;
use App\Notifications\Client\Orders\CancelOrder;
use App\Notifications\Client\Orders\OrderUpdateSettings;
use App\Notifications\Client\Orders\OrderUpdateStatus;
use App\Services\Cart\CartService;
use App\Services\Order\OrderService;
use App\Services\Refund\RefundService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;
use Mockery;
use Tests\TestCase;

class OrderServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->travelTo(now()->setTime(12, 0));

        $this->app->instance(
            PaymentProviderInterface::class,
            Mockery::mock(PaymentProviderInterface::class),
        );
    }

    public function test_online_order_creates_pending_remittance(): void
    {
        $user = User::factory()->create();
        $product = $this->product();
        app(CartService::class)->put($user, $product, 2);

        $order = app(OrderService::class)->create($user, [
            'type' => TypePaid::CARD_ONLINE->value,
            'need_delivery' => false,
        ]);

        $this->assertDatabaseHas('remittances', [
            'order_id' => $order->id,
            'amount' => '20.50',
            'status' => RemittanceStatus::AWAIT_PAID->value,
            'paid' => false,
        ]);
    }

    public function test_user_cannot_create_more_than_two_actual_orders(): void
    {
        Notification::fake();
        $user = User::factory()->create();

        foreach ([OrderStatus::NEW, OrderStatus::PROCESSING] as $status) {
            $this->order($user, $status);
        }

        app(CartService::class)->put($user, $this->product(), 1);

        try {
            app(OrderService::class)->create($user, [
                'type' => TypePaid::IN_PICKUP_LOCATION->value,
                'need_delivery' => false,
            ]);
            $this->fail('A third actual order was created.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('order', $exception->errors());
        }

        $this->assertSame(2, $user->orders()->actualStatus()->count());
        $this->assertSame(1, $user->products()->count());
        Notification::assertNothingSent();
    }

    public function test_delivery_and_courier_payment_require_an_address(): void
    {
        $service = app(OrderService::class);
        $user = User::factory()->create(['address' => null]);
        $product = $this->product();
        app(CartService::class)->put($user, $product, 1);

        foreach ([
            ['type' => TypePaid::IN_PICKUP_LOCATION->value, 'need_delivery' => true, 'field' => 'need_delivery'],
            ['type' => TypePaid::CARD_COURIER->value, 'need_delivery' => false, 'field' => 'type'],
        ] as $data) {
            try {
                $service->create($user, $data);
                $this->fail('An order with invalid delivery settings was created.');
            } catch (ValidationException $exception) {
                $this->assertArrayHasKey($data['field'], $exception->errors());
            }
        }

        $this->assertSame(0, $user->orders()->count());
    }

    public function test_order_status_follows_allowed_transitions(): void
    {
        Notification::fake();
        $service = app(OrderService::class);
        $order = $this->order(User::factory()->create());

        $service->updateStatus($order, OrderStatus::PROCESSING);
        $this->assertSame(OrderStatus::PROCESSING, $order->fresh()->status);

        $service->updateStatus($order->fresh(), OrderStatus::COMPLETED);
        $this->assertSame(OrderStatus::COMPLETED, $order->fresh()->status);
        Notification::assertSentToTimes($order->user, OrderUpdateStatus::class, 2);

        $this->expectException(ValidationException::class);
        $service->updateStatus($order->fresh(), OrderStatus::CANCELLED);
    }

    public function test_finishing_an_order_records_its_completion_time(): void
    {
        $service = app(OrderService::class);
        $completedOrder = $this->order(User::factory()->create(), OrderStatus::PROCESSING);
        $cancelledOrder = $this->order(User::factory()->create());
        $completedAt = Carbon::parse('2026-10-08 12:30:00');

        $this->travelTo($completedAt);

        $service->updateStatus($completedOrder, OrderStatus::COMPLETED);
        $service->updateStatus($cancelledOrder, OrderStatus::CANCELLED);

        $this->assertTrue($completedOrder->fresh()->completed_at->equalTo($completedAt));
        $this->assertTrue($cancelledOrder->fresh()->completed_at->equalTo($completedAt));

        $this->travelBack();
    }

    public function test_unpaid_online_order_cannot_start_processing(): void
    {
        $order = $this->order(User::factory()->create(), type: TypePaid::CARD_ONLINE);
        $order->remittance()->create(['amount' => $order->total_price]);

        $this->expectException(ValidationException::class);
        app(OrderService::class)->updateStatus($order, OrderStatus::PROCESSING);
    }

    public function test_online_order_without_remittance_can_be_cancelled(): void
    {
        $order = $this->order(User::factory()->create(), type: TypePaid::CARD_ONLINE);

        app(OrderService::class)->updateStatus($order, OrderStatus::CANCELLED);

        $this->assertSame(OrderStatus::CANCELLED, $order->fresh()->status);
    }

    public function test_canceling_paid_online_order_completes_refund(): void
    {
        $order = $this->order(User::factory()->create(), type: TypePaid::CARD_ONLINE);
        $remittance = $this->paidRemittance($order);
        $provider = $this->refundProvider('succeeded');
        $service = new OrderService(new RefundService($provider));

        $service->updateStatus($order, OrderStatus::CANCELLED);

        $this->assertSame(OrderStatus::CANCELLED, $order->fresh()->status);
        $this->assertSame(RemittanceStatus::REFUND, $remittance->fresh()->status);
        $this->assertSame(RefundStatus::SUCCEED, $remittance->refund()->firstOrFail()->status);
    }

    public function test_canceling_paid_online_order_fails_when_refund_is_rejected(): void
    {
        $order = $this->order(User::factory()->create(), type: TypePaid::CARD_ONLINE);
        $remittance = $this->paidRemittance($order);
        $provider = $this->refundProvider('canceled');
        $service = new OrderService(new RefundService($provider));

        try {
            $service->updateStatus($order, OrderStatus::CANCELLED);
            $this->fail('The order was canceled after the refund was rejected.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('order', $exception->errors());
        }

        $this->assertSame(OrderStatus::NEW, $order->fresh()->status);
        $this->assertSame(RemittanceStatus::PAID, $remittance->fresh()->status);
        $this->assertSame(RefundStatus::CANCELED, $remittance->refund()->firstOrFail()->status);
    }

    public function test_order_owner_can_cancel_an_unpaid_order(): void
    {
        Notification::fake();
        $user = User::factory()->create();
        $order = $this->order($user);
        $completedAt = Carbon::parse('2026-10-15 12:30:00');

        $this->actingAs($user);
        $this->travelTo($completedAt);

        app(OrderService::class)->cancel($order);

        $order->refresh();
        $this->assertSame(OrderStatus::CANCELLED, $order->status);
        $this->assertTrue($order->completed_at->equalTo($completedAt));
        Notification::assertSentTo($user, CancelOrder::class,
            fn (CancelOrder $notification): bool => $notification->order->is($order));

        $this->travelBack();
    }

    public function test_completed_or_cancelled_order_cannot_be_cancelled(): void
    {
        $user = User::factory()->create();
        $service = app(OrderService::class);

        $this->actingAs($user);

        foreach ([OrderStatus::COMPLETED, OrderStatus::CANCELLED] as $status) {
            $order = $this->order($user, $status);

            try {
                $service->cancel($order);
                $this->fail("An order with status {$status->value} was canceled again.");
            } catch (ValidationException $exception) {
                $this->assertArrayHasKey('order', $exception->errors());
            }

            $this->assertSame($status, $order->fresh()->status);
        }
    }

    public function test_canceling_online_order_removes_local_pending_remittance(): void
    {
        $user = User::factory()->create();
        $order = $this->order($user, type: TypePaid::CARD_ONLINE);
        $remittance = $order->remittances()->create([
            'amount' => $order->total_price,
            'status' => RemittanceStatus::AWAIT_PAID,
        ]);

        $this->actingAs($user);

        app(OrderService::class)->cancel($order);

        $this->assertSame(OrderStatus::CANCELLED, $order->fresh()->status);
        $this->assertDatabaseMissing('remittances', ['id' => $remittance->id]);
    }

    public function test_online_order_with_active_external_payment_cannot_be_cancelled(): void
    {
        $user = User::factory()->create();
        $order = $this->order($user, type: TypePaid::CARD_ONLINE);
        $remittance = $order->remittances()->create([
            'amount' => $order->total_price,
            'status' => RemittanceStatus::AWAIT_PAID,
            'payment_system_id' => '22d6d597-000f-5000-9000-145f6df21d70',
            'payment_system_status' => 'pending',
        ]);

        $this->actingAs($user);

        try {
            app(OrderService::class)->cancel($order);
            $this->fail('An order with an active external payment was canceled.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('order', $exception->errors());
        }

        $this->assertSame(OrderStatus::NEW, $order->fresh()->status);
        $this->assertNull($order->fresh()->completed_at);
        $this->assertDatabaseHas('remittances', ['id' => $remittance->id]);
    }

    public function test_canceled_external_payment_does_not_prevent_order_cancellation(): void
    {
        $user = User::factory()->create();
        $order = $this->order($user, type: TypePaid::CARD_ONLINE);
        $remittance = $order->remittances()->create([
            'amount' => $order->total_price,
            'status' => RemittanceStatus::AWAIT_PAID,
            'payment_system_id' => '22d6d597-000f-5000-9000-145f6df21d70',
            'payment_system_status' => 'canceled',
        ]);

        $this->actingAs($user);

        app(OrderService::class)->cancel($order);

        $this->assertSame(OrderStatus::CANCELLED, $order->fresh()->status);
        $this->assertNotNull($order->fresh()->completed_at);
        $this->assertDatabaseHas('remittances', ['id' => $remittance->id]);
    }

    public function test_canceling_paid_online_order_through_client_method_completes_refund(): void
    {
        $user = User::factory()->create();
        $order = $this->order($user, type: TypePaid::CARD_ONLINE);
        $remittance = $this->paidRemittance($order);
        $provider = $this->refundProvider('succeeded');
        $service = new OrderService(new RefundService($provider));

        $this->actingAs($user);

        $service->cancel($order);

        $this->assertSame(OrderStatus::CANCELLED, $order->fresh()->status);
        $this->assertSame(RemittanceStatus::REFUND, $remittance->fresh()->status);
        $this->assertSame(RefundStatus::SUCCEED, $remittance->refund()->firstOrFail()->status);
    }

    public function test_client_order_remains_active_when_refund_is_rejected(): void
    {
        $user = User::factory()->create();
        $order = $this->order($user, type: TypePaid::CARD_ONLINE);
        $remittance = $this->paidRemittance($order);
        $provider = $this->refundProvider('canceled');
        $service = new OrderService(new RefundService($provider));

        $this->actingAs($user);

        try {
            $service->cancel($order);
            $this->fail('The order was canceled after the refund was rejected.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('order', $exception->errors());
        }

        $this->assertSame(OrderStatus::NEW, $order->fresh()->status);
        $this->assertSame(RemittanceStatus::PAID, $remittance->fresh()->status);
        $this->assertSame(RefundStatus::CANCELED, $remittance->refund()->firstOrFail()->status);
    }

    public function test_settings_can_be_partially_updated_and_manage_remittance(): void
    {
        Notification::fake();
        $service = app(OrderService::class);
        $order = $this->order(User::factory()->create());

        $service->updateSettings($order, ['type' => TypePaid::CARD_ONLINE->value]);

        $this->assertSame(TypePaid::CARD_ONLINE, $order->fresh()->type_paid);
        $this->assertDatabaseHas('remittances', ['order_id' => $order->id]);
        $order->remittances()->create([
            'amount' => $order->total_price,
            'status' => RemittanceStatus::AWAIT_PAID,
        ]);
        $this->assertSame(2, $order->remittances()->count());

        $service->updateSettings($order->fresh(), ['need_delivery' => true]);
        $this->assertTrue($order->fresh()->need_delivery);

        $service->updateSettings($order->fresh(), ['type' => TypePaid::CASH_COURIER->value]);
        $this->assertSame(TypePaid::CASH_COURIER, $order->fresh()->type_paid);
        $this->assertDatabaseMissing('remittances', ['order_id' => $order->id]);
        Notification::assertSentToTimes($order->user, OrderUpdateSettings::class, 3);
        Notification::assertSentTo($order->user, OrderUpdateSettings::class,
            fn (OrderUpdateSettings $notification): bool => $notification->payload['typePaid'] === TypePaid::IN_PICKUP_LOCATION
                && $notification->payload['needDelivery'] === false);
    }

    public function test_paid_online_order_payment_type_cannot_be_changed(): void
    {
        $order = $this->order(User::factory()->create(), type: TypePaid::CARD_ONLINE);
        $order->remittance()->create([
            'amount' => $order->total_price,
            'paid' => true,
            'status' => RemittanceStatus::PAID,
        ]);
        $order->remittances()->create([
            'amount' => $order->total_price,
            'status' => RemittanceStatus::AWAIT_PAID,
        ]);

        $this->expectException(ValidationException::class);

        app(OrderService::class)->updateSettings($order, [
            'type' => TypePaid::IN_PICKUP_LOCATION->value,
        ]);
    }

    public function test_payment_type_cannot_be_changed_while_payment_form_is_active(): void
    {
        $order = $this->order(User::factory()->create(), type: TypePaid::CARD_ONLINE);
        $remittance = $order->remittances()->create([
            'amount' => $order->total_price,
            'status' => RemittanceStatus::AWAIT_PAID,
            'payment_system_id' => '22d6d597-000f-5000-9000-145f6df21d70',
            'payment_system_status' => 'pending',
        ]);

        try {
            app(OrderService::class)->updateSettings($order, [
                'type' => TypePaid::IN_PICKUP_LOCATION->value,
            ]);
            $this->fail('The payment type was changed while a payment form was active.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('type', $exception->errors());
        }

        $this->assertSame(TypePaid::CARD_ONLINE, $order->fresh()->type_paid);
        $this->assertDatabaseHas('remittances', ['id' => $remittance->id]);
    }

    public function test_payment_types_are_filtered_when_address_is_missing(): void
    {
        $service = app(OrderService::class);
        $withoutAddress = User::factory()->create(['address' => '   ']);
        $withAddress = User::factory()->create();

        $this->assertSame(
            [TypePaid::CARD_ONLINE->value, TypePaid::IN_PICKUP_LOCATION->value],
            $service->getTypePaid($withoutAddress)->keys()->all(),
        );
        $this->assertSame(array_keys(TypePaid::TEXTS), $service->getTypePaid($withAddress)->keys()->all());
    }

    private function order(
        User $user,
        OrderStatus $status = OrderStatus::NEW,
        TypePaid $type = TypePaid::IN_PICKUP_LOCATION,
    ): Order {
        return Order::create([
            'user_id' => $user->id,
            'total_price' => '10.25',
            'status' => $status,
            'type_paid' => $type,
            'need_delivery' => false,
        ]);
    }

    private function product(): Product
    {
        $category = Category::firstOrCreate(
            ['url' => 'sushi-rolls'],
            ['title' => 'Sushi rolls'],
        );

        return Product::create([
            'title' => 'California roll',
            'price' => '10.25',
            'category_id' => $category->id,
            'active' => true,
        ]);
    }

    private function paidRemittance(Order $order): Remittance
    {
        return $order->remittances()->create([
            'amount' => $order->total_price,
            'currency' => 'RUB',
            'paid' => true,
            'status' => RemittanceStatus::PAID,
            'payment_system_id' => '22d6d597-000f-5000-9000-145f6df21d6f',
            'payment_system_status' => 'succeeded',
        ]);
    }

    private function refundProvider(string $status): PaymentProviderInterface
    {
        $provider = Mockery::mock(PaymentProviderInterface::class);
        $provider->shouldReceive('refund')
            ->once()
            ->with(Mockery::type(Remittance::class), Mockery::type(Refund::class))
            ->andReturn(new RemittanceRefundDTO(
                refundId: '216749f7-0016-50be-b000-078d43a63ae4',
                status: $status,
                reason: 'Возврат средств за заказ',
                amount: '10.25',
            ));

        return $provider;
    }
}
