<?php

namespace App\Http\RequestDTO\Product\Client;

use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class ProductsClientQueryFilters extends Data
{
    public function __construct(
        #[Min(1), Max(255)]
        public Optional|string $title,
    ) {
    }
}
