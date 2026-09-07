<?php

namespace App\Http\RequestDTO\Tags\Admin;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class TagsQueryFilters extends Data
{
    public function __construct(
        public Optional|string $url,
        public Optional|string $title
    ) {
    }
}
