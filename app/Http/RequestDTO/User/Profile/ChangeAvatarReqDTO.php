<?php

namespace App\Http\RequestDTO\User\Profile;

use Illuminate\Http\UploadedFile;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\MergeValidationRules;
use Spatie\LaravelData\Data;

#[MergeValidationRules]
class ChangeAvatarReqDTO extends Data
{

    public function __construct(
        public string $item,
        public UploadedFile $image,
    ) {
    }

    public static function rules(): array
    {
        return [
            'item' => Rule::in(array_keys(config('app.imageables', []))),
            'image' => 'image|mimes:jpeg,png,jpg,webp|max:2048',
        ];
    }
}
