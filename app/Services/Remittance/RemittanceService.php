<?php

namespace App\Services\Remittance;

use App\Enums\Orders\TypePaid;
use App\Enums\Remittances\Status;
use App\Models\Order;
use App\Models\Remittance;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

class RemittanceService
{
    public function store(Order $order): string
    {
        Gate::authorize('view', $order);

        if ($order->type_paid !== TypePaid::CARD_ONLINE) {
            throw ValidationException::withMessages([
                'payment' => 'Онлайн-оплата недоступна для выбранного типа оплаты.',
            ]);
        }

        $remittance = $order->remittance()->firstOrCreate([], [
            'amount' => $order->total_price,
            'status' => Status::AWAIT_PAID,
        ]);

        if ($remittance->paid || $remittance->status !== Status::AWAIT_PAID) {
            throw ValidationException::withMessages([
                'payment' => 'Оплата уже выполнена или ожидает подтверждения.',
            ]);
        }

        return $this->getPaymentUrl($remittance);
    }

    public function getPaymentUrl(Remittance $remittance): string
    {
        throw ValidationException::withMessages([
            'payment' => 'Онлайн-оплата временно недоступна.',
        ]);
    }
}
