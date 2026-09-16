<?php

namespace App\Http\RequestDTO\User\Phone;

use App\Models\Phone;
use Spatie\LaravelData\Attributes\MergeValidationRules;
use Spatie\LaravelData\Attributes\Validation\Regex;
use Spatie\LaravelData\Attributes\Validation\Unique;
use Spatie\LaravelData\Data;

#[MergeValidationRules]
class AddPhoneReqDTO extends Data
{
    public function __construct(
        #[Regex('/^7\d{10}$/'), Unique(Phone::class, 'phone')]
        public string $phone
    ) {
    }

    public static function messages(): array
    {
        return [
            'phone.regex' => 'Введите номер в формате 79991234567.',
            'phone.unique' => 'Такой телефон уже занят',
        ];
    }
}
