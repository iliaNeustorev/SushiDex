<?php

namespace App\Http\Resources\Products;

use App\Http\Resources\Images\ImagePublicResource;
use App\Models\Product;
use Spatie\LaravelData\Attributes\Computed;
use Spatie\LaravelData\Data;

class ProductOrderItemResource extends Data
{
    #[Computed]
    public string $calculateCountPrice;

    public function __construct(
        public int $id,
        public string $title,
        public int $count,
        public string $price,
        public ?ImagePublicResource $previewImage,
    ) {
        $this->calculateCountPrice = $count * (float) $price;
    }

    public static function fromProduct(Product $product): self
    {
        return self::from([
            ...$product->attributesToArray(),
            'price' => $product->pivot->price,
            'previewImage' => $product->previewImage,
            'count' => (int) $product->pivot->count,
        ]);
    }
}
