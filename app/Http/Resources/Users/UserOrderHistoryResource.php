<?php

namespace App\Http\Resources\Users;

use Spatie\LaravelData\Data;

class UserOrderHistoryResource extends Data
{
    public function __construct(
        public int $id,
        public string $first_name,
        public ?string $last_name,
        public ?string $middle_name,
    ) {}
}
