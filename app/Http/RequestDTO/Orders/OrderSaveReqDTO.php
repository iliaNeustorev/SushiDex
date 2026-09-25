<?php

namespace App\Http\RequestDTO\Orders;

use App\Enums\Orders\TypePaid;
use Spatie\LaravelData\Data;

class OrderSaveReqDTO extends Data
{
    public function __construct(
        public TypePaid $type,
        public bool $need_delivery,
    ) {
    }
}
