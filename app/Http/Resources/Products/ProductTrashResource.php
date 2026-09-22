<?php

namespace App\Http\Resources\Products;

use App\Http\Resources\Categories\CategoryCrudResource;
use Carbon\Carbon;
use Spatie\LaravelData\Data;

class ProductTrashResource extends Data
{
    public function __construct(
        public int $id,
        public string $title,
        public string $price,
        public CategoryCrudResource $category,
        public Carbon $created_at,
    ) {}
}
