<?php

namespace App\Integrations\PaymentProvider;

use AllowDynamicProperties;
use App\Models\Order;
use App\Models\Remittance;
use YooKassa\Client;

#[AllowDynamicProperties]
class YKassa
{

    /**
     * @throws \Exception
     */
    public function __construct(
        public Client $client,
    ) {
        $this->key = config('ykassa.key');
        $this->shopId = config('ykassa.shop_id');
        if (is_null($this->key) || is_null($this->shopId)) {
            throw new \Exception('YKassa key and shopId cannot be null');
        }
    }

    public function store(Remittance $remittance, Order $order): array
    {
        $this->client->setAuth($this->shopId, $this->key);
        $payment = $this->client->createPayment(
            array(
                'amount' => array(
                    'value' => $remittance->amount,
                    'currency' => $remittance->currency,
                ),
                'confirmation' => array(
                    'type' => 'redirect',
                    'return_url' => url('orders'),
                ),
                'capture' => true,
                'description' => "Заказ №{$order->id}",
            ),
            'Remittance-' . $remittance->id
        );
        return [
            'url' => $payment?->confirmation?->confirmation_url,
            'external_id' => $payment?->id,
            'external_status' => $payment?->status,
            'external_amount' => $payment?->amount?->value,
        ];
    }
}
