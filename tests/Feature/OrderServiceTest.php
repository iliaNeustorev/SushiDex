<?php

namespace Tests\Feature;

use App\Enums\Orders\Status as OrderStatus;
use App\Enums\Orders\TypePaid;
use App\Enums\Remittances\Status as RemittanceStatus;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Services\Cart\CartService;
use App\Services\Order\OrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class OrderServiceTest extends TestCase
{
    use RefreshDatabase;

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
        $service = app(OrderService::class);
        $order = $this->order(User::factory()->create());

        $service->updateStatus($order, OrderStatus::PROCESSING);
        $this->assertSame(OrderStatus::PROCESSING, $order->fresh()->status);

        $service->updateStatus($order->fresh(), OrderStatus::COMPLETED);
        $this->assertSame(OrderStatus::COMPLETED, $order->fresh()->status);

        $this->expectException(ValidationException::class);
        $service->updateStatus($order->fresh(), OrderStatus::CANCELLED);
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

    public function test_settings_can_be_partially_updated_and_manage_remittance(): void
    {
        $service = app(OrderService::class);
        $order = $this->order(User::factory()->create());

        $service->updateSettings($order, ['type' => TypePaid::CARD_ONLINE->value]);

        $this->assertSame(TypePaid::CARD_ONLINE, $order->fresh()->type_paid);
        $this->assertDatabaseHas('remittances', ['order_id' => $order->id]);

        $service->updateSettings($order->fresh(), ['need_delivery' => true]);
        $this->assertTrue($order->fresh()->need_delivery);

        $service->updateSettings($order->fresh(), ['type' => TypePaid::CASH_COURIER->value]);
        $this->assertSame(TypePaid::CASH_COURIER, $order->fresh()->type_paid);
        $this->assertDatabaseMissing('remittances', ['order_id' => $order->id]);
    }

    public function test_paid_online_order_payment_type_cannot_be_changed(): void
    {
        $order = $this->order(User::factory()->create(), type: TypePaid::CARD_ONLINE);
        $order->remittance()->create([
            'amount' => $order->total_price,
            'paid' => true,
            'status' => RemittanceStatus::PAID,
        ]);

        $this->expectException(ValidationException::class);

        app(OrderService::class)->updateSettings($order, [
            'type' => TypePaid::IN_PICKUP_LOCATION->value,
        ]);
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
}
