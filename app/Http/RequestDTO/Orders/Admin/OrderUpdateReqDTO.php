<?php

namespace App\Http\RequestDTO\Orders\Admin;

use App\Enums\Orders\Status;
use Spatie\LaravelData\Data;

class OrderUpdateReqDTO extends Data
{
    public function __construct(
        public Status $status,
    ) {}
}
