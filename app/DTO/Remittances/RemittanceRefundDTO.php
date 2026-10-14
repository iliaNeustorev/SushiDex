<?php

namespace App\DTO\Remittances;

final readonly class RemittanceRefundDTO
{
    public function __construct(
        public string $refundId,
        public string $status,
        public string $reason,
        public string $amount,
    ) {}
}
