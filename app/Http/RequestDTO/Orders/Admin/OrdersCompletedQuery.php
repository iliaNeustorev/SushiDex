<?php

namespace App\Http\RequestDTO\Orders\Admin;

use Spatie\LaravelData\Attributes\Validation\In;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class OrdersCompletedQuery extends Data
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public Optional|OrdersCompletedQueryFilters $filter,
        public Optional|string $sort,

        #[Min(1)]
        public Optional|int $page,

        #[In(10, 20, 50)]
        public Optional|int $batch,
    ) {}
}
