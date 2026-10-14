<?php

namespace App\DTO\Remittances;

final readonly class RemittanceWebhookDTO
{
    public const STATUS_SUCCEEDED = 'success';

    public const STATUS_CANCELED = 'canceled';

    public const STATUS_WAITING = 'waiting';

    public const STATUS_REFUND = 'refund.succeeded';

    public const STATUS_REFUND_FAILED = 'refund.failed';

    public function __construct(
        public string $paymentSystemId,
        public string $status,
        public string $externalStatus,
        public ?string $refundSystemId = null
    ) {}
}
