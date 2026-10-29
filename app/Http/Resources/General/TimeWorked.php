<?php

namespace App\Http\Resources\General;

use Carbon\Carbon;
use Spatie\LaravelData\Data;

class TimeWorked extends Data
{
    public function __construct(
        public bool $isOpen,
        public Carbon $serverNow,
        public Carbon $nextChangeAt,
        public string $startWork,
        public string $endWork,
    ) {
    }
}
