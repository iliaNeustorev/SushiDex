<?php

namespace App\Http\RequestDTO\Tags\Admin;

use Spatie\LaravelData\Attributes\Validation\AlphaDash;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Unique;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Support\Validation\References\RouteParameterReference;

class TagsSaveReqDTO extends Data
{
    public function __construct(
        #[
            Min(3),
            Max((16)),
            AlphaDash,
            Unique('tags', ignore: new RouteParameterReference('tag', 'id', true))
        ]
        public string $url,

        #[
            Min(3),
            Max((32))
        ]
        public string $title,

        #[
            Min(10),
            Max((1024))
        ]
        public ?string $description,
    ) {
    }
}
