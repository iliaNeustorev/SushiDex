<?php

namespace App\Http\Resources\Phone\Client;

use Carbon\Carbon;
use Spatie\LaravelData\Data;

class PhoneProfileResource extends Data
{
    public function __construct(
        public string $phone,
        public Carbon $verified_at
    ) {}
}
