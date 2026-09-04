<?php

namespace App\Http\RequestDTO\Category\Admin;

use App\Enums\Categories\Type;
use Spatie\LaravelData\Attributes\Validation\AlphaDash;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\NotIn;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Unique;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Support\Validation\References\RouteParameterReference;

class CategoriesSaveReqDTO extends Data
{
    public function __construct(
        #[
            Min(5),
            Max(255),
            AlphaDash,
            Unique('categories', ignore: new RouteParameterReference('category', 'id', true))
        ]
        public string $url,

        #[
            Min(5),
            Max(255)
        ]
        public string $title,

        #[Nullable]
        public ?Type $type,

        #[
            Nullable,
            Exists('categories', 'id'),
            NotIn(new RouteParameterReference('category', 'id', true))
        ]
        public ?int $parent_id,
    ) {
    }
}
