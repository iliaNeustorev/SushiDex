<?php

namespace App\Http\Resources\Products;

use App\Http\Resources\Categories\CategoryCrudResource;
use Carbon\Carbon;
use Spatie\LaravelData\Data;

class ProductCrudResource extends Data
{
    public function __construct(
        public int $id,
        public string $title,
        public ?string $description,
        public ?string $content,
        public string $price,
        public ?string $old_price,
        public CategoryCrudResource $category,
        public Carbon $created_at,
        public Carbon $updated_at,
    ) {}
}
