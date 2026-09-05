<?php

namespace App\Http\RequestDTO\Category\Admin;

use App\Enums\Categories\Type;
use Spatie\LaravelData\Attributes\Validation\DateFormat;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class CategoriesQueryFilters extends Data
{
    public function __construct(

        public Optional|string $title,
        public Optional|string $url,
        public Optional|Type $type,

        #[DateFormat('Y-m-d')]
        public Optional|string $date_from,

        #[DateFormat('Y-m-d')]
        public Optional|string $date_to,
    ) {}
}
