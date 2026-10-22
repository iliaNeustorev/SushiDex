<?php

namespace Tests\Feature;

use App\Enums\Orders\TypePaid;
use App\Models\Order;
use App\Models\User;
use App\Notifications\Admin\OrderCreated as AdminOrderCreated;
use App\Notifications\Client\OrderCreated;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\Channels\DatabaseChannel;
use Illuminate\Support\Str;
use Tests\TestCase;

class OrderNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_slack_card_contains_order_details_and_primary_url_button(): void
    {
        foreach ([true, false] as $delivery) {
            $order = new Order(['id' => 123, 'total_price' => '2500.50',
                'type_paid' => TypePaid::CARD_ONLINE, 'need_delivery' => $delivery]);
            $notification = new AdminOrderCreated($order);
            $payload = $notification->toSlack(new \stdClass)->toArray();

            $this->assertTrue($notification->afterCommit);
            $this->assertSame(['slack'], $notification->via(new \stdClass));
            $this->assertStringContainsString('2500.50 ₽', $payload['text']);
            $this->assertStringContainsString($delivery ? 'Доставка' : 'Самовывоз', $payload['blocks'][2]['fields'][2]['text']);
            $button = $payload['blocks'][4]['elements'][0];
            $this->assertSame('button', $button['type']);
            $this->assertSame('primary', $button['style']);
            $this->assertSame(route('admin.orders.actual'), $button['url']);
        }
    }

    public function test_database_notification_and_delayed_mail_respect_read_status(): void
    {
        $this->freezeTime();
        $user = User::factory()->create();
        $other = User::factory()->create();
        $order = new Order(['id' => 123, 'total_price' => '2500.50']);
        $notification = new OrderCreated($order);
        $notification->id = Str::uuid()->toString();

        $this->assertTrue($notification->afterCommit);
        $this->assertSame(['mail', 'database'], $notification->via($user));
        $this->assertTrue($notification->withDelay($user)['mail']->equalTo(now()->addMinutes(5)));
        app(DatabaseChannel::class)->send($user, $notification);
        $saved = $user->notifications()->sole();
        $this->assertSame('/orders/123', $saved->data['url']);
        $this->assertTrue($notification->shouldSend($user, 'mail'));
        $saved->markAsRead();
        $this->assertFalse($notification->shouldSend($user, 'mail'));
        $this->assertTrue($notification->shouldSend($user, 'database'));
        $this->assertTrue($notification->shouldSend($other, 'mail'));
        $this->assertTrue((new OrderCreated(new Order(['id' => 456])))->shouldSend($user, 'mail'));
        $this->assertSame(url('/orders/123'), $notification->toMail($user)->actionUrl);
    }
}
