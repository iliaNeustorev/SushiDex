<?php

namespace App\Http\Resources\Carts;

use App\Http\Resources\Categories\CategoryPublicResource;
use App\Http\Resources\Images\ImagePublicResource;
use App\Models\Product;
use Spatie\LaravelData\Data;

class CartPublicDetailsResource extends Data
{
    public function __construct(
        public int $id,
        public string $title,
        public string $price,
        public ?string $description,
        public ?string $content,
        public CategoryPublicResource $category,
        public ?ImagePublicResource $previewImage,
        public ?string $old_price,
        public int $count,
    ) {
    }

    public static function fromProduct(Product $product): self
    {
        return self::from([
            ...$product->attributesToArray(),
            'category' => $product->category,
            'previewImage' => $product->previewImage,
            'count' => (int)$product->pivot->count,
        ]);
    }
}
