<?php

namespace App\Http\RequestDTO\Carts;

use Spatie\LaravelData\Data;

class CartSyncTempReqDTO extends Data
{
    public function __construct(
        public array $tempCart,
    ) {}

    public static function rules(): array
    {
        return [
            'tempCart' => 'required|array',
            'tempCart.*.product_id' => 'required|integer|exists:products,id',
            'tempCart.*.count' => 'required|integer|min:1|max:100',
        ];
    }
}
