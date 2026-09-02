<?php

namespace App\Http\RequestDTO\Posts\Admin;

use App\Enums\Posts\Status;
use Spatie\LaravelData\Attributes\Validation\DateFormat;
use Spatie\LaravelData\Attributes\Validation\Regex;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class PostsQueryFilters extends Data
{
    public function __construct(

        public Optional|string $title,
        public Optional|Status $status,

        #[DateFormat('Y-m-d')]
        public Optional|string $date_from,

        #[DateFormat('Y-m-d')]
        public Optional|string $date_to,

        #[Regex('/^(\d+,)*\d+$/')]
        public Optional|string $tags
    ) {}
}
