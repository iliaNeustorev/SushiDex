<?php

namespace App\Http\Resources\Carts;

use App\Models\Product;
use Spatie\LaravelData\Data;

class CartPublicResource extends Data
{
    public function __construct(
        public int $id,
        public int $count,
        public string $price,
        public string $title,
    ) {
    }

    public static function fromProduct(Product $product): self
    {
        return self::from([
            ...$product->attributesToArray(),
            'count' => (int)$product->pivot->count,
        ]);
    }
}
