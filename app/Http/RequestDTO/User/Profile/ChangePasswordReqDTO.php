<?php

namespace App\Http\RequestDTO\User\Profile;

use Spatie\LaravelData\Attributes\Validation\Confirmed;
use Spatie\LaravelData\Attributes\Validation\CurrentPassword;
use Spatie\LaravelData\Attributes\Validation\Password;
use Spatie\LaravelData\Data;

class ChangePasswordReqDTO extends Data
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        #[CurrentPassword]
        public string $current_password,
        #[Password(8), Confirmed]
        public string $password,
        public string $password_confirmation
    ) {}
}
