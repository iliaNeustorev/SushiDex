<?php

namespace App\Http\RequestDTO\User\Admin;

use Spatie\LaravelData\Attributes\Validation\DateFormat;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class UsersQueryFilters extends Data
{
    public function __construct(
        public Optional|string $name,
        public Optional|string $email,
        public Optional|string $phone,
        public Optional|bool $block,
        public Optional|string $address,

        #[DateFormat('Y-m-d')]
        public Optional|string $date_from,

        #[DateFormat('Y-m-d')]
        public Optional|string $date_to,
    ) {
    }
}
