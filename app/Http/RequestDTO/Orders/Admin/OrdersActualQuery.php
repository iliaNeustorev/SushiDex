<?php

namespace App\Http\RequestDTO\Orders\Admin;

use Spatie\LaravelData\Attributes\Validation\In;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class OrdersActualQuery extends Data
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public Optional|OrdersActualQueryFilters $filter,

        #[Min(1)]
        public Optional|int $actualPage,

        #[In(10, 20, 50)]
        public Optional|int $actualBatch,

        #[Min(1)]
        public Optional|int $processingPage,

        #[In(10, 20, 50)]
        public Optional|int $processingBatch,
    ) {}
}
