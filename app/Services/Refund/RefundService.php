<?php

namespace App\Services\Refund;

use App\Enums\Refunds\Status;
use App\Interfaces\PaymentProviderInterface;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RefundService
{
    public function __construct(
        private PaymentProviderInterface $paymentProvider
    ) {}

    public function store(Order $order, string $reason = 'Возврат средств за заказ'): void
    {
        $transaction = DB::transaction(function () use ($order, $reason) {
            $remittance = $order
                ->remittances()
                ->where('paid', true)
                ->lockForUpdate()
                ->firstOrFail();

            $refund = $remittance
                ->refund()
                ->firstOrCreate([], [
                    'amount' => $remittance->amount,
                    'reason' => $reason,
                ]);

            return compact('refund', 'remittance');
        });
        if ($transaction['refund']->status === Status::SUCCEED) {
            return;
        }
        $resultStore = $this->paymentProvider->refund($transaction['remittance'], $transaction['refund']);
        $transaction['refund']->update([
            'external_system_status' => $resultStore->status,
            'external_system_id' => $resultStore->refundId,
        ]
        );
        if ($resultStore->status === 'succeeded') {
            DB::transaction(function () use ($transaction) {
                $transaction['refund']->update([
                    'status' => Status::SUCCEED,
                    'refunded_at' => Carbon::now(),
                ]);

                $transaction['remittance']->update([
                    'status' => \App\Enums\Remittances\Status::REFUND,
                ]);
            });

            return;
        }
        if ($resultStore->status === 'canceled') {
            $transaction['refund']->update([
                'status' => Status::CANCELED,
            ]);

            throw ValidationException::withMessages([
                'refund' => 'Платёжная система отклонила возврат.',
            ]);
        }
    }
}
