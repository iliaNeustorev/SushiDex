<?php

namespace App\Http\RequestDTO\Category\Admin;

use Spatie\LaravelData\Attributes\Validation\In;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class CategoriesTrashQuery extends Data
{
    public function __construct(
        public Optional|CategoriesTrashFiltersQuery $filter,
        public Optional|string $sort,

        #[Min(1)]
        public Optional|int $page,

        #[In(10, 20, 50)]
        public Optional|int $batch,
    ) {}
}
