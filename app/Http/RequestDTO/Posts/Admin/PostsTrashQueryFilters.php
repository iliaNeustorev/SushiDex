<?php

namespace App\Http\RequestDTO\Posts\Admin;

use Spatie\LaravelData\Attributes\Validation\DateFormat;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class PostsTrashQueryFilters extends Data
{
    public function __construct(
        public Optional|string $title,

        #[DateFormat('Y-m-d')]
        public Optional|string $date_from,

        #[DateFormat('Y-m-d')]
        public Optional|string $date_to,
    ) {}
}
