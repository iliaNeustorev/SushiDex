<?php

namespace App\Notifications\Admin\Orders;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Slack\BlockKit\Blocks\ActionsBlock;
use Illuminate\Notifications\Slack\BlockKit\Blocks\ContextBlock;
use Illuminate\Notifications\Slack\BlockKit\Blocks\SectionBlock;
use Illuminate\Notifications\Slack\SlackMessage;

class OrderCreated extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Количество попыток выполнения уведомления.
     *
     * @var int
     */
    public $tries = 5;

    /**
     * Количество секунд, в течение которых уведомление может выполняться до истечения времени ожидания.
     *
     * @var int
     */
    public $timeout = 60;

    /**
     * Максимальное количество необработанных исключений до завершения с ошибкой.
     *
     * @var int
     */
    public $maxExceptions = 3;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Order $order
    ) {
        $this->afterCommit();
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['slack'];
    }

    public function toSlack(object $notifiable): SlackMessage
    {
        return (new SlackMessage)
            ->text("Новый заказ №{$this->order->id} на сумму {$this->order->total_price} ₽")
            ->headerBlock("🍣 Новый заказ №{$this->order->id}")
            ->sectionBlock(function (SectionBlock $block): void {
                $block->text('Поступил новый заказ. Проверьте детали и примите его в работу.');
            })
            ->sectionBlock(function (SectionBlock $block): void {
                $block->field("*Сумма заказа*\n{$this->order->total_price} ₽")->markdown();
                $block->field("*Оплата*\n{$this->order->type_paid->text()}")->markdown();
                $block->field("*Получение*\n".($this->order->need_delivery ? '🚚 Доставка' : '🥡 Самовывоз'))->markdown();
            })
            ->dividerBlock()
            ->actionsBlock(function (ActionsBlock $block): void {
                $block->button('Открыть заказы')
                    ->id('open_actual_orders')
                    ->url(route('admin.orders.actual'))
                    ->primary();
            })
            ->contextBlock(function (ContextBlock $block): void {
                $block->text('SushiDex · Уведомление для администратора');
            })
            ->unfurlLinks(false)
            ->unfurlMedia(false);
    }
}
