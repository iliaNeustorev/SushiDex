<?php

namespace App\Http\Resources\Products;

use App\Models\Product;
use Spatie\LaravelData\Attributes\Computed;
use Spatie\LaravelData\Data;

class ProductOrderItemWithoutImageResource extends Data
{
    #[Computed]
    public string $calculateCountPrice;

    public function __construct(
        public int $id,
        public string $title,
        public int $count,
        public string $price,
    ) {
        $this->calculateCountPrice = $count * (float) $price;
    }

    public static function fromProduct(Product $product): self
    {
        return self::from([
            ...$product->attributesToArray(),
            'price' => $product->pivot->price,
            'count' => (int) $product->pivot->count,
        ]);
    }
}
