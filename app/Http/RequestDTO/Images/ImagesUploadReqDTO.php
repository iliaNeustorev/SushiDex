<?php

namespace App\Http\RequestDTO\Images;

use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\MergeValidationRules;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\LiteralTypeScriptType;

#[MergeValidationRules]
class ImagesUploadReqDTO extends Data
{
    public function __construct(
        public string $item,
        public int $id,

        #[Min(1), Max(5), LiteralTypeScriptType('File[]')]
        public array $images,
    ) {}

    public static function rules(): array
    {
        return [
            'item' => Rule::in(array_keys(config('app.imageables', []))),
            'images.*' => 'required|image|mimes:jpeg,png,jpg,webp|max:10048',
        ];
    }
}
