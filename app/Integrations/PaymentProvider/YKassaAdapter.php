<?php

namespace App\Integrations\PaymentProvider;

use App\Interfaces\PaymentProviderInterface;
use App\Models\Order;
use App\Models\Remittance;

class YKassaAdapter implements PaymentProviderInterface
{

    public function __construct(
        private readonly YKassa $ykassa
    ) {
    }

    public function store(Remittance $remittance, Order $order): array
    {
        return $this->ykassa->store($remittance, $order);
    }
}
