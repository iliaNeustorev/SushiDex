<?php

namespace App\Http\RequestDTO\Posts\Admin;

use Spatie\LaravelData\Attributes\Validation\In;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class PostsQuery extends Data
{
    public function __construct(
        public Optional|PostsQueryFilters $filter,
        public Optional|string $sort,

        #[Min(1)]
        public Optional|int $page,

        #[In(10, 20, 50)]
        public Optional|int $batch,

        public Optional|string $tagSearch
    ) {}
}
