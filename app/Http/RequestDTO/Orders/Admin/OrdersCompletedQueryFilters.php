<?php

namespace App\Http\RequestDTO\Orders\Admin;

use App\Enums\Orders\Status;
use App\Enums\Orders\TypePaid;
use App\Models\Order;
use Spatie\LaravelData\Attributes\Validation\DateFormat;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class OrdersCompletedQueryFilters extends Data
{
    public function __construct(
        #[Exists(Order::class, 'id')]
        public Optional|int $id,

        public Optional|Status $status,
        public Optional|TypePaid $type_paid,

        #[DateFormat('Y-m-d')]
        public Optional|string $date_created_from,

        #[DateFormat('Y-m-d')]
        public Optional|string $date_created_to,

        #[DateFormat('Y-m-d')]
        public Optional|string $date_completed_from,

        #[DateFormat('Y-m-d')]
        public Optional|string $date_completed_to,
    ) {}

    public static function messages(): array
    {
        return [
            'id.exists' => 'Неправильный номер заказа',
        ];
    }
}
