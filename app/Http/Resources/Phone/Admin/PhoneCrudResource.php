<?php

namespace App\Http\Resources\Phone\Admin;

use Carbon\Carbon;
use Spatie\LaravelData\Data;

class PhoneCrudResource extends Data
{
    public function __construct(
        public string $phone,
        public Carbon $verified_at
    ) {
    }
}
