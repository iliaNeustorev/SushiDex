<?php

namespace App\Http\Resources\Remittances;

use App\Enums\Remittances\Status;
use Spatie\LaravelData\Data;

class RemittancePublicResource extends Data
{
    public function __construct(
        public int $id,
        public bool $paid,
        public string $amount,
        public Status $status
    ) {}
}
