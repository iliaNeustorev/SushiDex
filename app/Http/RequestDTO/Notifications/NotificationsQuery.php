<?php

namespace App\Http\RequestDTO\Notifications;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class NotificationsQuery extends Data
{
    public function __construct(
        public Optional|bool $all
    ) {}
}
