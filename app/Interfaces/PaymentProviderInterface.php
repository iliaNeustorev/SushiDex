<?php

namespace App\Interfaces;

use App\Models\Order;
use App\Models\Remittance;

interface PaymentProviderInterface
{
    public function store(Remittance $remittance, Order $order): array;
}
