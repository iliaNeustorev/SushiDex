<?php

namespace App\Http\RequestDTO\Orders\Admin;

use App\Models\Order;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class OrdersActualQueryFilters extends Data
{
    public function __construct(
        #[Exists(Order::class, 'id')]
        public Optional|int $id,
    ) {}
}
