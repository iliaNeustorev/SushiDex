<?php

namespace App\Http\RequestDTO\Notifications;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class NotificationsUpdateDTO extends Data
{
    public function __construct(
        public Optional|bool $redirect
    ) {}
}
