<?php

namespace App\Http\Resources\Posts;

use App\Enums\Posts\Status;
use App\Http\Resources\Categories\CategoryCrudResource;
use Carbon\Carbon;
use Spatie\LaravelData\Data;

class PostTrashResource extends Data
{
    public function __construct(
        public int $id,
        public string $url,
        public string $title,
        public Carbon $created_at,
        public Status $status,
        public CategoryCrudResource $category,
    ) {}
}
