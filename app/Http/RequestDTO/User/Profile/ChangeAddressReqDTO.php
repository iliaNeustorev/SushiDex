<?php

namespace App\Http\RequestDTO\User\Profile;

use Spatie\LaravelData\Data;

class ChangeAddressReqDTO extends Data
{
    public function __construct(
        public string $address,
    ) {}
}
