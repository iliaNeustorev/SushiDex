<?php

namespace App\Http\RequestDTO\Posts\Admin;

use App\Models\Category;
use App\Models\Tag;
use App\Rules\CheckModelIds;
use App\Rules\SoftExists;
use Spatie\LaravelData\Attributes\Validation\AlphaDash;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Rule;
use Spatie\LaravelData\Attributes\Validation\Unique;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Support\Validation\References\RouteParameterReference;

class PostsSaveReqDTO extends Data
{
    public function __construct(
        #[
            Min(3),
            Max((16)),
            AlphaDash,
            Unique('posts', ignore: new RouteParameterReference('post', 'id', true))
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
        public string $content,

        #[Rule(new SoftExists(Category::class))]
        public int $category_id,

        #[Rule(new CheckModelIds(Tag::class))]
        public ?array $tags,
    ) {
    }
}
