<?php

namespace App\Http\RequestDTO\Product\Admin;

use App\Models\Category;
use Spatie\LaravelData\Attributes\Validation\DateFormat;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class ProductsQueryFilters extends Data
{
    public function __construct(
        public Optional|string $title,

        #[Exists(Category::class, 'id')]
        public Optional|int $category_id,

        #[DateFormat('Y-m-d')]
        public Optional|string $date_from,

        #[DateFormat('Y-m-d')]
        public Optional|string $date_to,
    ) {}
}
