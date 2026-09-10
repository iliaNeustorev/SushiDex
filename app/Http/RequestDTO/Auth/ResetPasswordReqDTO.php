<?php

namespace App\Http\RequestDTO\Auth;

use Spatie\LaravelData\Attributes\Validation\Confirmed;
use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Attributes\Validation\Password;
use Spatie\LaravelData\Data;

class ResetPasswordReqDTO extends Data
{
    public function __construct(
        public string $token,
        #[Email]
        public ?string $email,
        #[Password(8), Confirmed]
        public string $password,
        public string $password_confirmation,
    ) {
    }
}
