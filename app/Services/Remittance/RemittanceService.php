<?php

namespace App\Services\Remittance;

use App\DTO\Remittances\RemittanceWebhookDTO;
use App\Enums\Orders\TypePaid;
use App\Enums\Remittances\Status;
use App\Interfaces\PaymentProviderInterface;
use App\Models\Order;
use App\Models\Remittance;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class RemittanceService
{
    public function __construct(
        private PaymentProviderInterface $paymentProvider
    ) {}

    public function store(Order $order): string
    {
        Gate::authorize('view', $order);

        if ($order->type_paid !== TypePaid::CARD_ONLINE) {
            throw ValidationException::withMessages([
                'payment' => 'Онлайн-оплата недоступна для выбранного типа оплаты.',
            ]);
        }

        $remittance = DB::transaction(function () use ($order) {
            $lockedOrder = Order::whereKey($order->id)
                ->lockForUpdate()
                ->firstOrFail();

            if (
                $lockedOrder->remittances()
                    ->where('status', Status::PAID)
                    ->exists()
            ) {
                throw ValidationException::withMessages([
                    'payment' => 'Заказ уже оплачен.',
                ]);
            }

            $activeRemittance = $lockedOrder->remittances()
                ->where('status', Status::AWAIT_PAID)
                ->latest()
                ->first();

            return $activeRemittance
                ?? $lockedOrder->remittances()->create([
                    'amount' => $lockedOrder->total_price,
                    'status' => Status::AWAIT_PAID,
                ]);
        });

        try {
            $result = $this->paymentProvider->store($remittance, $order);
        } catch (Exception $e) {
            report($e);
            throw ValidationException::withMessages([
                'payment' => 'Во время оплаты произошла ошибка. Попробуйте еще раз',
            ]);
        }
        if (isset($result['external_status'], $result['external_id'], $result['url'], $result['external_amount'])) {
            $remittance->update([
                'payment_system_status' => $result['external_status'],
                'payment_system_id' => $result['external_id'],
                'payment_system_amount' => $result['external_amount'],
            ]);

            return $result['url'];
        }
        throw ValidationException::withMessages([
            'payment' => 'Во время оплаты произошла ошибка. Попробуйте еще раз',
        ]);
    }

    /**
     * @throws \Throwable
     */
    public function handleWebhook(array $payload): void
    {
        $resultHandle = $this->paymentProvider->handleWebhook($payload);

        DB::transaction(function () use ($resultHandle) {
            $remittance = Remittance::where('payment_system_id', $resultHandle->paymentSystemId)->lockForUpdate()->first();
            if ($remittance) {
                if (
                    $resultHandle->status === RemittanceWebhookDTO::STATUS_SUCCEEDED
                    && ! $remittance->paid
                    && $remittance->status !== Status::PAID
                ) {
                    $remittance->update([
                        'paid' => true,
                        'payment_system_status' => RemittanceWebhookDTO::STATUS_SUCCEEDED,
                        'payed_at' => Carbon::now(),
                        'status' => Status::PAID,
                    ]);
                }
                if ($resultHandle->status === RemittanceWebhookDTO::STATUS_WAITING && ! $remittance->paid && $remittance->status !== Status::PAID) {
                    $remittance->update([
                        'payment_system_status' => RemittanceWebhookDTO::STATUS_WAITING,
                    ]);
                }
                if ($resultHandle->status === RemittanceWebhookDTO::STATUS_CANCELED && ! $remittance->paid && $remittance->status !== Status::PAID) {
                    $remittance->update([
                        'payment_system_status' => RemittanceWebhookDTO::STATUS_CANCELED,
                    ]);
                }
            } else {
                throw new Exception('Не найдена оплата id '.$resultHandle->paymentSystemId);
            }
        });

        Log::info($payload);
    }
}
