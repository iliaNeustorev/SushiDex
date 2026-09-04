<?php

namespace App\Http\Resources\Categories;

use App\Enums\Categories\Type;
use Spatie\LaravelData\Data;

class CategoryPublicResource extends Data
{
    public function __construct(
        public string $url,
        public string $title,
        public Type $type,
        public ?int $parent_id
    ) {
    }
}
