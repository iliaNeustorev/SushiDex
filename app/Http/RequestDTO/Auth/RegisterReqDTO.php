<?php

namespace App\Http\RequestDTO\Auth;

use App\Helpers\PhoneNormalizer;
use Spatie\LaravelData\Attributes\Validation\Confirmed;
use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Password;
use Spatie\LaravelData\Attributes\Validation\Regex;
use Spatie\LaravelData\Attributes\Validation\Unique;
use Spatie\LaravelData\Data;

class RegisterReqDTO extends Data
{
    public function __construct(
        #[Min(3), Max(255)]
        public string $first_name,
        #[Email, Unique('users')]
        public string $email,
        #[Password(8), Confirmed]
        public string $password,
        public string $password_confirmation,
        #[Regex('/^7\d{10}$/'), Unique('phones')]
        public ?string $phone
    ) {
    }

    public static function prepareForPipeline(array $properties): array
    {
        return array_replace($properties, [
            'phone' => isset($properties['phone']) ? PhoneNormalizer::normalize($properties['phone']) : null,
        ]);
    }
}
