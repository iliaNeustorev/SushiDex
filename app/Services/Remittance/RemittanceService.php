<?php

namespace App\Services\Remittance;

use App\DTO\Remittances\RemittanceWebhookDTO;
use App\Enums\Orders\Status as OrderStatus;
use App\Enums\Orders\TypePaid;
use App\Enums\Refunds\Status as RefundStatus;
use App\Enums\Remittances\Status;
use App\Interfaces\PaymentProviderInterface;
use App\Models\Order;
use App\Models\Refund;
use App\Models\Remittance;
use App\Notifications\Client\Remittances\Refund as NotifyRefund;
use App\Notifications\Client\Remittances\Success;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Throwable;

class RemittanceService
{
    public function __construct(
        private PaymentProviderInterface $paymentProvider
    ) {}

    public function store(Order $order): string
    {
        Gate::authorize('view', $order);

        $remittance = DB::transaction(function () use ($order) {
            $lockedOrder = Order::whereKey($order->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($lockedOrder->type_paid !== TypePaid::CARD_ONLINE) {
                throw ValidationException::withMessages([
                    'payment' => 'Онлайн-оплата недоступна для выбранного типа оплаты.',
                ]);
            }

            if (in_array($lockedOrder->status, [OrderStatus::COMPLETED, OrderStatus::CANCELLED], true)) {
                throw ValidationException::withMessages([
                    'payment' => 'Оплата недоступна для текущего статуса заказа.',
                ]);
            }

            if (
                $lockedOrder->remittances()
                    ->where('paid', true)
                    ->exists()
            ) {
                throw ValidationException::withMessages([
                    'payment' => 'Заказ уже оплачен.',
                ]);
            }
            $activeRemittance = $lockedOrder->remittances()
                ->whereNull('payment_system_id')
                ->whereNull('payment_system_status')
                ->first();

            if (is_null($activeRemittance)) {
                $activeRemittance = $lockedOrder->remittances()->create([
                    'amount' => $lockedOrder->total_price,
                    'status' => Status::AWAIT_PAID,
                ]);
            }

            return $activeRemittance;
        });

        try {
            $newPayment = $this->paymentProvider->store($remittance, $order);
        } catch (Throwable $e) {
            report($e);
            throw ValidationException::withMessages([
                'payment' => 'Во время оплаты произошла ошибка. Попробуйте еще раз',
            ]);
        }
        if (isset($newPayment->status, $newPayment->paymentId, $newPayment->url, $newPayment->amount)) {
            $remittance->update([
                'payment_system_status' => $newPayment->status,
                'payment_system_id' => $newPayment->paymentId,
                'payment_system_amount' => $newPayment->amount,
            ]);

            return $newPayment->url;
        }
        throw ValidationException::withMessages([
            'payment' => 'Во время оплаты произошла ошибка. Попробуйте еще раз',
        ]);
    }

    /**
     * @throws Throwable
     */
    public function handleWebhook(array $payload): void
    {
        $resultHandle = $this->paymentProvider->handleWebhook($payload);
        if ($resultHandle->status === RemittanceWebhookDTO::STATUS_REFUND) {
            $this->handleRefundWebhook($resultHandle);
        } else {
            $this->handleRemittanceWebhook($resultHandle);
        }
        Log::info($payload);
    }

    private function handleRefundWebhook(RemittanceWebhookDTO $payloadWebhook): void
    {
        DB::transaction(function () use ($payloadWebhook) {
            $refund = Refund::with('remittance.order')
                ->where('external_system_id', $payloadWebhook->refundSystemId)
                ->lockForUpdate()
                ->firstOrFail();
            $remittance = $refund->remittance()
                ->where('payment_system_id', $payloadWebhook->paymentSystemId)
                ->lockForUpdate()
                ->firstOrFail();
            if ($refund->status === RefundStatus::SUCCEED && $remittance->status === Status::REFUND) {
                return;
            }

            $refund->update([
                'external_system_status' => $payloadWebhook->externalStatus,
                'refunded_at' => Carbon::now(),
                'status' => RefundStatus::SUCCEED,
            ]);
            $remittance->update([
                'status' => Status::REFUND,
            ]);

            $remittance->loadMissing('order.user');
            $remittance->order->user->notify(new NotifyRefund($remittance));
        });
    }

    private function handleRemittanceWebhook(RemittanceWebhookDTO $payloadWebhook): void
    {
        DB::transaction(function () use ($payloadWebhook) {
            $remittance = Remittance::where('payment_system_id', $payloadWebhook->paymentSystemId)
                ->lockForUpdate()
                ->first();
            if ($remittance) {
                if (
                    $payloadWebhook->status === RemittanceWebhookDTO::STATUS_SUCCEEDED
                    && ! $remittance->paid
                    && $remittance->status !== Status::PAID
                ) {
                    $remittance->update([
                        'paid' => true,
                        'payment_system_status' => $payloadWebhook->externalStatus,
                        'payed_at' => Carbon::now(),
                        'status' => Status::PAID,
                    ]);
                    $remittance->loadMissing('order.user');
                    $remittance->order->user->notify(new Success($remittance));
                }
                if ($payloadWebhook->status === RemittanceWebhookDTO::STATUS_WAITING && ! $remittance->paid && $remittance->status !== Status::PAID) {
                    $remittance->update([
                        'payment_system_status' => $payloadWebhook->externalStatus,
                    ]);
                }
                if ($payloadWebhook->status === RemittanceWebhookDTO::STATUS_CANCELED && ! $remittance->paid && $remittance->status !== Status::PAID) {
                    $remittance->update([
                        'payment_system_status' => $payloadWebhook->externalStatus,
                    ]);
                }
            } else {
                throw new Exception('Не найдена оплата id '.$payloadWebhook->paymentSystemId);
            }
        });
    }
}
