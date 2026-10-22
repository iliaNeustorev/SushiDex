<?php

namespace App\Http\Resources\Notifications;

use Carbon\Carbon;
use Spatie\LaravelData\Data;

class DatabaseNotificationResource extends Data
{
    public function __construct(
        public string $id,
        public string $type,
        public NotificationPayloadResource $data,
        public ?Carbon $read_at,
        public Carbon $created_at,
    ) {}
}
