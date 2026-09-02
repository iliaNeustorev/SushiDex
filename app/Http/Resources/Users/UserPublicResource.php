<?php

namespace App\Http\Resources\Users;

use Spatie\LaravelData\Data;

class UserPublicResource extends Data
{
    public function __construct(
        public int $id,
        public string $first_name
    ) {}
}
