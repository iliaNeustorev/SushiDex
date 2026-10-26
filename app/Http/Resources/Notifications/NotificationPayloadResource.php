<?php

namespace App\Http\Resources\Notifications;

use Spatie\LaravelData\Data;

class NotificationPayloadResource extends Data
{
    public function __construct(
        public string $message,
        public string $url,
        public string $sender,
        public string $type,
    ) {}
}
