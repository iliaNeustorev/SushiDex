<?php

namespace App\Http\Resources\Orders\Admin;

use App\Enums\Orders\Status;
use App\Enums\Orders\TypePaid;
use App\Http\Resources\Products\ProductOrderItemWithoutImageResource;
use App\Http\Resources\Remittances\RemittancePublicResource;
use App\Http\Resources\Users\UserOrderHistoryResource;
use Carbon\Carbon;
use Spatie\LaravelData\Attributes\Computed;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

class OrderAdminHistoryResource extends Data
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
        public ?Carbon $completed_at,
        #[DataCollectionOf(ProductOrderItemWithoutImageResource::class)]
        public DataCollection $products,
        public UserOrderHistoryResource $user,
        public ?RemittancePublicResource $remittance,
    ) {
        $this->status_text = $this->status->text();
        $this->type_paid_text = $this->type_paid->text();
    }
}
