<?php

namespace App\Http\RequestDTO\User\Profile;

use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Data;

class ProfileSaveReqDTO extends Data
{
    public function __construct(
        #[Min(3), Max(255)]
        public string $first_name,
        #[Min(3), Max(255)]
        public ?string $middle_name,
        #[Min(3), Max(255)]
        public ?string $last_name,
        #[Min(3), Max(255)]
        public ?string $address,
    ) {}
}
