<?php

namespace App\Interfaces;

use App\DTO\Remittances\RemittanceWebhookDTO;
use App\Models\Order;
use App\Models\Remittance;

interface PaymentProviderInterface
{
    public function store(Remittance $remittance, Order $order): array;

    public function handleWebhook(array $payload): RemittanceWebhookDTO;
}
