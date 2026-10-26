<?php

namespace App\Notifications\Client\Orders;

use App\Enums\Orders\Status;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class OrderUpdateStatus extends Notification implements ShouldQueue
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
        return [
            'message' => $this->formMessage($this->order),
            'url' => '/orders/'.$this->order->id,
            'type' => $this->formType($this->order),
            'sender' => 'Система',
        ];
    }

    private function formMessage(Order $order): string
    {
        return match ($order->status) {
            Status::PROCESSING => "Ваш заказ № {$order->id} принят в работу!",
            Status::COMPLETED => "Ваш заказ № {$order->id} завершен!",
            Status::CANCELLED => "Ваш заказ № {$order->id} отменен!",
        };
    }

    private function formType(Order $order): string
    {
        return match ($order->status) {
            Status::PROCESSING, Status::COMPLETED => 'success',
            Status::CANCELLED => 'error',
        };
    }
}
