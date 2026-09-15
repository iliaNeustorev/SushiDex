<?php

namespace App\Http\Resources\Orders\Client;

use App\Enums\Orders\Status;
use App\Enums\Orders\TypePaid;
use Carbon\Carbon;
use Spatie\LaravelData\Attributes\Computed;
use Spatie\LaravelData\Data;

class OrderPublicResource extends Data
{
    #[Computed]
    public string $status_text;

    #[Computed]
    public string $type_paid_text;

    public function __construct(
        public int $id,
        public string $total_price,
        public Status $status,
        public TypePaid $type_paid,
        public bool $need_delivery,
        public Carbon $created_at,
        public int $items_count,
    ) {
        $this->status_text = $this->status->text();
        $this->type_paid_text = $this->type_paid->text();
    }
}
