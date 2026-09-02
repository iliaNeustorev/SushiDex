<?php

namespace App\Http\RequestDTO\Carts;

use App\Models\Product;
use App\Rules\SoftExists;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Rule;
use Spatie\LaravelData\Data;

class CartSaveReqDTO extends Data
{
    public function __construct(
        #[Rule(new SoftExists(Product::class))]
        public int $product_id,

        #[Min(1), Max(100)]
        public int $count,
    ) {}
}
