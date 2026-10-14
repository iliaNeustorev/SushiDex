<?php

namespace App\Integrations\PaymentProvider;

use App\Models\Order;
use App\Models\Refund;
use App\Models\Remittance;
use RuntimeException;
use YooKassa\Client;

class YKassa
{
    public function __construct(
        private Client $client,
    ) {}

    public function getClient(): Client
    {
        $key = config('ykassa.key');
        $shopId = config('ykassa.shop_id');

        if (! is_string($key) || $key === '' || ! is_string($shopId) || $shopId === '') {
            throw new RuntimeException('YKassa key and shopId cannot be empty');
        }

        return $this->client->setAuth($shopId, $key);
    }

    public function store(Remittance $remittance, Order $order): array
    {
        $client = $this->getClient();
        $payment = $client->createPayment(
            [
                'amount' => [
                    'value' => $remittance->amount,
                    'currency' => $remittance->currency,
                ],
                'confirmation' => [
                    'type' => 'redirect',
                    'return_url' => url('orders'),
                ],
                'capture' => true,
                'description' => "Заказ №{$order->id}",
            ],
            'Remittance-'.$remittance->id
        );

        return [
            'url' => $payment?->confirmation?->confirmation_url,
            'id' => $payment?->id,
            'status' => $payment?->status,
            'amount' => $payment?->amount?->value,
        ];
    }

    public function createRefund(Remittance $remittance, Refund $refund): array
    {
        $client = $this->getClient();
        $refund = $client->createRefund(
            [
                'amount' => [
                    'value' => $remittance->amount,
                    'currency' => $remittance->currency,
                ],
                'payment_id' => $remittance->payment_system_id,
                'metadata' => [
                    'reason' => $refund->reason,
                ],
            ],
            'refund-'.$refund->id,
        );

        return [
            'id' => $refund?->id,
            'status' => $refund?->status,
            'amount' => $refund?->amount?->value,
            'reason' => $refund?->metadata?->reason,
        ];
    }
}
