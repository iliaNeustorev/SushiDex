<?php

namespace App\Http\RequestDTO\Orders;

use App\Enums\Orders\Status;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\MergeValidationRules;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Data;

#[MergeValidationRules]
class OrderSaveReqDTO extends Data
{
    public function __construct(
        #[Exists('users', 'id')]
        public int $user_id,

        public string $total_price,
        public Status $status
    ) {}

    public function rules(): array
    {
        return [
            'total_price' => [
                Rule::numeric()
                    ->decimal(0, 2)
                    ->between(0.01, 100_000),
            ],
        ];
    }
}
