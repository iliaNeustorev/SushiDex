<?php

namespace App\Http\RequestDTO\Orders;

use App\Enums\Orders\TypePaid;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class OrderUpdateSettingsReqDTO extends Data
{
    public function __construct(
        public Optional|TypePaid $type,
        public Optional|bool $need_delivery,
    ) {}
}
