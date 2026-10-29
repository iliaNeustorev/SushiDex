<?php

namespace Tests\Feature;

use App\Enums\Categories\Type;
use App\Enums\Orders\TypePaid;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use App\Services\Cart\CartService;
use App\Services\Order\OrderService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class OrderWorkTimeTest extends TestCase
{
    use RefreshDatabase;

    public static function workTimes(): array
    {
        return [
            'before opening' => ['2026-10-29 10:59:59', false, '2026-10-29 11:00:00'],
            'opening' => ['2026-10-29 11:00:00', true, '2026-10-29 23:00:00'],
            'before closing' => ['2026-10-29 22:59:59', true, '2026-10-29 23:00:00'],
            'closing' => ['2026-10-29 23:00:00', false, '2026-10-30 11:00:00'],
            'month boundary' => ['2026-10-31 23:59:59', false, '2026-11-01 11:00:00'],
            'midnight' => ['2026-10-30 00:00:00', false, '2026-10-30 11:00:00'],
        ];
    }

    #[DataProvider('workTimes')]
    public function test_work_time_boundaries(string $now, bool $isOpen, string $nextChange): void
    {
        $this->travelTo(Carbon::parse($now));

        $time = app(OrderService::class)->getWorkTime();

        $this->assertSame($isOpen, $time->isOpen);
        $this->assertTrue($time->serverNow->equalTo(Carbon::parse($now)));
        $this->assertTrue($time->nextChangeAt->equalTo(Carbon::parse($nextChange)));
        $this->assertSame('11:00', $time->startWork);
        $this->assertSame('23:00', $time->endWork);
    }

    #[DataProvider('workTimes')]
    public function test_order_creation_obeys_work_hours_and_preserves_cart_when_closed(string $now, bool $isOpen, string $nextChange): void
    {
        $this->travelTo(Carbon::parse($now));
        $user = User::factory()->create();
        $category = Category::create(['url' => 'food', 'title' => 'Еда', 'type' => Type::PRODUCT]);
        $product = Product::create([
            'title' => 'Суши',
            'content' => 'Описание товара',
            'price' => '150.00',
            'active' => true,
            'category_id' => $category->id,
        ]);
        app(CartService::class)->put($user, $product, 2);

        $response = $this->actingAs($user)->post(route('orders.store'), [
            'type' => TypePaid::IN_PICKUP_LOCATION->value,
            'need_delivery' => false,
        ]);

        if ($isOpen) {
            $order = $user->orders()->sole();
            $response->assertRedirect(route('orders.show', $order));
            $this->assertSame('300.00', $order->total_price);
            $this->assertSame(0, $user->products()->count());
        } else {
            $response->assertSessionHasErrors('order');
            $this->assertSame(0, $user->orders()->count());
            $this->assertSame(2, $user->products()->sole()->pivot->count);
        }
    }
}
