<?php

namespace App\Http\Resources\Posts;

use App\Http\Resources\Categories\CategoryPublicResource;
use App\Http\Resources\Tags\TagPublicResource;
use App\Http\Resources\Users\UserPublicResource;
use Carbon\Carbon;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

class PostPublicResource extends Data
{
    public function __construct(
        public int $id,
        public string $url,
        public ?string $title,
        public ?string $content,
        public Carbon $created_at,
        public ?CategoryPublicResource $category,
        public ?UserPublicResource $user,
        #[DataCollectionOf(TagPublicResource::class)]
        public DataCollection $tags,
    ) {}
}
