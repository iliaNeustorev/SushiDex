<?php

namespace App\Http\RequestDTO\Auth;

use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Data;

class LoginReqDTO extends Data
{
    public function __construct(
        #[Email]
        public string $email,
        public string $password,
        public bool $remember = false
    ) {
    }
}
