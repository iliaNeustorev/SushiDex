<?php

namespace App\Notifications\Client\Remittances;

use App\Models\Remittance;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class Refund extends Notification implements ShouldQueue
{
    use Queueable;

    public $tries = 5;

    public $timeout = 60;

    public $maxExceptions = 3;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Remittance $remittance)
    {
        $this->afterCommit();
    }

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
        $order = $this->remittance->order;

        return [
            'message' => "Выполнен возврат оплаты по заказу № {$order->id}!",
            'url' => '/orders/'.$order->id,
            'type' => 'success',
            'sender' => 'Система',
        ];
    }
}
