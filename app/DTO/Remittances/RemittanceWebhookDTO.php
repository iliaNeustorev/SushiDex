<?php

namespace App\DTO\Remittances;

final readonly class RemittanceWebhookDTO
{
    public const STATUS_SUCCEEDED = 'success';

    public const STATUS_CANCELED = 'canceled';

    public const STATUS_WAITING = 'waiting';

    public function __construct(
        public string $paymentSystemId,
        public string $status
    ) {}
}
