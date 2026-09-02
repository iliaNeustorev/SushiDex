<?php

namespace App\Http\RequestDTO\Carts;

use Illuminate\Validation\Rule;
use Spatie\LaravelData\Data;

class CartSaveReqDTO extends Data
{
    public function __construct(
        public int $product_id,
        public int $count,
    ) {}

    public static function rules(): array
    {
        return [
            'product_id' => ['required', 'integer', Rule::exists('products', 'id')->whereNull('deleted_at')],
            'count' => ['required', 'integer', 'min:1', 'max:100'],
        ];
    }
}
