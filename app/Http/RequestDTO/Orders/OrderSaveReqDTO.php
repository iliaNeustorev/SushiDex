<?php

namespace App\Http\RequestDTO\Orders;

use App\Enums\Orders\TypePaid;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\MergeValidationRules;
use Spatie\LaravelData\Data;

#[MergeValidationRules]
class OrderSaveReqDTO extends Data
{
    public function __construct(
        public TypePaid $type,
        public bool $need_delivery,
        public string $total_price,
    ) {
    }

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
