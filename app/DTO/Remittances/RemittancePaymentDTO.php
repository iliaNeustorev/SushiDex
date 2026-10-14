<?php

namespace App\DTO\Remittances;

final readonly class RemittancePaymentDTO
{
    public function __construct(
        public string $paymentId,
        public string $status,
        public string $amount,
        public string $url,
    ) {}
}
