<?php

namespace App\Http\Resources\Phone\Client;

use Carbon\Carbon;
use Spatie\LaravelData\Data;

class PendingPhoneProfileResource extends Data
{
    public function __construct(
        public int $id,
        public string $phone,
        public Carbon $created_at,
    ) {}
}
