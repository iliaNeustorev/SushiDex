<?php

namespace App\Http\RequestDTO\Product\Admin;

use Spatie\LaravelData\Data;

class ProductsChangeActiveDTO extends Data
{
    public function __construct(
        public bool $active,
    ) {}
}
