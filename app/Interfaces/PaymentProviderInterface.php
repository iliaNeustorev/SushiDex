<?php

namespace App\Interfaces;

use App\DTO\Remittances\RemittancePaymentDTO;
use App\DTO\Remittances\RemittanceRefundDTO;
use App\DTO\Remittances\RemittanceWebhookDTO;
use App\Models\Order;
use App\Models\Refund;
use App\Models\Remittance;

interface PaymentProviderInterface
{
    public function store(Remittance $remittance, Order $order): RemittancePaymentDTO;

    public function handleWebhook(array $payload): RemittanceWebhookDTO;

    public function refund(Remittance $remittance, Refund $refund): RemittanceRefundDTO;
}
