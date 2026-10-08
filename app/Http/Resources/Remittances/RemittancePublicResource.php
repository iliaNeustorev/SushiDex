<?php

namespace App\Http\Resources\Remittances;

use App\Enums\Remittances\Status;
use Spatie\LaravelData\Attributes\Computed;
use Spatie\LaravelData\Data;

class RemittancePublicResource extends Data
{
    #[Computed]
    public string $status_text;

    public function __construct(
        public int $id,
        public bool $paid,
        public string $amount,
        public Status $status
    ) {
        $this->status_text = $status->text();
    }
}
