<?php

namespace App\Integrations\PaymentProvider;

use App\DTO\Remittances\RemittanceWebhookDTO;
use App\Interfaces\PaymentProviderInterface;
use App\Models\Order;
use App\Models\Remittance;
use Exception;
use InvalidArgumentException;
use YooKassa\Model\Notification\NotificationCanceled;
use YooKassa\Model\Notification\NotificationEventType;
use YooKassa\Model\Notification\NotificationSucceeded;
use YooKassa\Model\Notification\NotificationWaitingForCapture;

class YKassaAdapter implements PaymentProviderInterface
{
    public function __construct(
        private readonly YKassa $ykassa
    ) {}

    public function store(Remittance $remittance, Order $order): array
    {
        return $this->ykassa->store($remittance, $order);
    }

    /**
     * @throws Exception
     */
    public function handleWebhook(array $payload): RemittanceWebhookDTO
    {
        $notification = match ($payload['event']) {
            NotificationEventType::PAYMENT_SUCCEEDED => new NotificationSucceeded($payload),

            NotificationEventType::PAYMENT_WAITING_FOR_CAPTURE => new NotificationWaitingForCapture($payload),

            NotificationEventType::PAYMENT_CANCELED => new NotificationCanceled($payload),

            default => throw new InvalidArgumentException(
                'Неподдерживаемое событие: '.$payload['event'],
            ),
        };

        $payment = $notification->getObject();

        $status = match ($notification->getEvent()) {
            NotificationEventType::PAYMENT_SUCCEEDED => RemittanceWebhookDTO::STATUS_SUCCEEDED,
            NotificationEventType::PAYMENT_CANCELED => RemittanceWebhookDTO::STATUS_CANCELED,
            NotificationEventType::PAYMENT_WAITING_FOR_CAPTURE => RemittanceWebhookDTO::STATUS_WAITING,
            default => throw new InvalidArgumentException(
                'Неподдерживаемое событие: '.$notification->getEvent(),
            ),
        };

        return new RemittanceWebhookDTO(
            paymentSystemId: $payment->getId(),
            status: $status,
        );
    }
}
