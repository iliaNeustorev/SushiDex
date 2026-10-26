<?php

namespace App\Notifications\Client\Orders;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class OrderUpdateSettings extends Notification implements ShouldQueue
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
        public Order $order,
        public array $payload = []
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $typePaid = $this->payload['typePaid'] !== $this->order->type_paid ? "Текущий тип оплаты: {$this->order->type_paid->text()};" : '';
        $needDelivery = $this->payload['needDelivery'] !== $this->order->need_delivery ? 'Доставка: '.($this->order->need_delivery ? 'Да' : 'Нет') : '';

        return [
            'message' => "Параметры вашего заказа изменены: $typePaid $needDelivery",
            'url' => '/orders/'.$this->order->id,
            'type' => 'warning',
            'sender' => 'Система',
        ];
    }
}
