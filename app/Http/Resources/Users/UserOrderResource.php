<?php

namespace App\Http\Resources\Users;

use App\Http\Resources\Phone\Client\PhoneProfileResource;
use Spatie\LaravelData\Data;

class UserOrderResource extends Data
{
    public function __construct(
        public int $id,
        public string $first_name,
        public ?string $last_name,
        public ?string $middle_name,
        public ?string $email,
        public ?string $address,
        public ?PhoneProfileResource $phone,
    ) {}
}
