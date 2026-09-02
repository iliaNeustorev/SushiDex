<?php

namespace App\Http\Resources\Posts;

use App\Enums\Posts\Status;
use App\Http\Resources\Categories\CategoryCrudResource;
use App\Http\Resources\Tags\TagCrudResource;
use Carbon\Carbon;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

class PostCrudResource extends Data
{
    public function __construct(
        public int $id,
        public string $url,
        public string $title,
        public string $content,
        public Carbon $created_at,
        public Status $status,
        public ?CategoryCrudResource $category,
        #[DataCollectionOf(TagCrudResource::class)]
        public DataCollection $tags,
    ) {}
}
