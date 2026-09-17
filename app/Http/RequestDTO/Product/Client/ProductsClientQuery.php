<?php

namespace App\Http\RequestDTO\Product\Client;

use App\Enums\Categories\Type;
use App\Models\Category;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;
use Spatie\LaravelData\Support\Validation\Constraints\WhereConstraint;

class ProductsClientQuery extends Data
{
    public function __construct(
        public Optional|ProductsClientQueryFilters $filter,

        #[Min(1), Max(255), Exists(Category::class, 'url', withoutTrashed: true, where: new WhereConstraint('type', Type::PRODUCT))]
        public Optional|string $url,

        #[Min(1)]
        public Optional|int $page,
    ) {
    }
}
