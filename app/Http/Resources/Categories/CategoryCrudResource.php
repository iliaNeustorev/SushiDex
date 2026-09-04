<?php

namespace App\Http\Resources\Categories;

use App\Enums\Categories\Type;
use Carbon\Carbon;
use Spatie\LaravelData\Data;

class CategoryCrudResource extends Data
{
    public function __construct(
        public int $id,
        public string $url,
        public string $title,
        public Carbon $created_at,
        public Type $type,
        public ?int $parent_id
    ) {
    }
}
