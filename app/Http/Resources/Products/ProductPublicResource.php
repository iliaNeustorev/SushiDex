<?php

namespace App\Http\Resources\Products;

use App\Http\Resources\Images\ImagePublicResource;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

class ProductPublicResource extends Data
{
    public function __construct(
        public int $id,
        public string $title,
        public ?string $description,
        public ?string $content,
        public string $price,
        public ?string $old_price,
        public ?int $count_paid,
        public bool $active,
        #[DataCollectionOf(ImagePublicResource::class)]
        public DataCollection $images,
    ) {
    }
}
