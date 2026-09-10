<?php

namespace App\Http\Resources\Phone\Admin;

use App\Http\Resources\Users\UserPublicResource;
use Carbon\Carbon;
use Spatie\LaravelData\Data;

class PhoneCrudResource extends Data
{
    public function __construct(
        public ?UserPublicResource $user,
        public string $phone,
        public Carbon $verified_at
    ) {
    }
}
