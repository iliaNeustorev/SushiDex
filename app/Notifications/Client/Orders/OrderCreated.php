<?php

namespace App\Notifications\Client\Orders;

use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

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
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $url = url('/orders/' . $this->order->id);

        return (new MailMessage)
            ->greeting('Здравствуйте!')
            ->subject('Новый заказ')
            ->line("Создан новый заказ на сумму: {$this->order->total_price}")
            ->action('Посмотреть заказ', $url)
            ->line('Спасибо что вы с нами!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'message' => 'Создан новый заказ на сумму ' . $this->order->total_price . 'р. !',
            'url' => '/orders/' . $this->order->id,
            'type' => 'success',
            'sender' => 'Система',
        ];
    }

    /**
     * Определение задержки доставки уведомления.
     *
     * @return array<string, \Illuminate\Support\Carbon>
     */
    public function withDelay(object $notifiable): array
    {
        return [
            'mail' => Carbon::now()->plus(minutes: 1),
        ];
    }

    public function shouldSend(object $notifiable, string $channel): bool
    {
        if ($channel !== 'mail') {
            return true;
        }
        return !$notifiable->notifications()
            ->where('id', $this->id)
            ->whereNotNull('read_at')
            ->exists();
    }
}
