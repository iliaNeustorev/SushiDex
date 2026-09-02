<?php

namespace App\Http\RequestDTO\Product\Admin;

use App\Models\Category;
use App\Rules\SoftExists;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\MergeValidationRules;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Rule as SpatieRule;
use Spatie\LaravelData\Data;

#[MergeValidationRules]
class ProductsSaveReqDTO extends Data
{
    public function __construct(
        #[
            Min(5),
            Max(255),
        ]
        public string $title,

        #[
            Nullable,
            Min(5),
        ]
        public ?string $description,

        #[
            Nullable,
            Min(5),
        ]
        public ?string $content,
        public string $price,
        public ?string $old_price,
        #[SpatieRule(new SoftExists(Category::class))]
        public int $category_id,
    ) {}

    public static function rules(): array
    {
        return [
            'price' => Rule::numeric()
                ->decimal(0, 2)
                ->between(0.01, 100_000),
            'old_price' => [
                'nullable',
                Rule::numeric()
                    ->decimal(0, 2)
                    ->between(0.01, 100_000),
                'gte:price',
            ],
        ];
    }
}
