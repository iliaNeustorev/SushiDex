<?php

namespace App\Integrations\PaymentProvider;

use App\DTO\Remittances\RemittancePaymentDTO;
use App\DTO\Remittances\RemittanceRefundDTO;
use App\DTO\Remittances\RemittanceWebhookDTO;
use App\Interfaces\PaymentProviderInterface;
use App\Models\Order;
use App\Models\Refund;
use App\Models\Remittance;
use Exception;
use InvalidArgumentException;
use YooKassa\Model\Notification\NotificationEventType;
use YooKassa\Model\Notification\NotificationFactory;

class YKassaAdapter implements PaymentProviderInterface
{
    public function __construct(
        private readonly YKassa $ykassa
    ) {}

    public function store(Remittance $remittance, Order $order): RemittancePaymentDTO
    {
        $result = $this->ykassa->store($remittance, $order);

        return new RemittancePaymentDTO(
            paymentId: $result['id'],
            status: $result['status'],
            amount: $result['amount'],
            url: $result['url'],
        );
    }

    /**
     * @throws Exception
     */
    public function handleWebhook(array $payload): RemittanceWebhookDTO
    {
        $factory = new NotificationFactory;
        $notificationObject = $factory->factory($payload);
        $responseObject = $notificationObject->getObject();

        if ($notificationObject->getEvent() === NotificationEventType::PAYMENT_SUCCEEDED) {
            $someData = [
                'paymentId' => $responseObject->getId(),
                'paymentStatus' => $responseObject->getStatus(),
            ];
        } elseif ($notificationObject->getEvent() === NotificationEventType::PAYMENT_WAITING_FOR_CAPTURE) {
            $someData = [
                'paymentId' => $responseObject->getId(),
                'paymentStatus' => $responseObject->getStatus(),
            ];
        } elseif ($notificationObject->getEvent() === NotificationEventType::PAYMENT_CANCELED) {
            $someData = [
                'paymentId' => $responseObject->getId(),
                'paymentStatus' => $responseObject->getStatus(),
            ];
        } elseif ($notificationObject->getEvent() === NotificationEventType::REFUND_SUCCEEDED) {
            $someData = [
                'refundId' => $responseObject->getId(),
                'paymentId' => $responseObject->getPaymentId(),
                'paymentStatus' => $responseObject->getStatus(),
            ];
        } else {
            throw new InvalidArgumentException(
                'Неподдерживаемое событие: '.$notificationObject->getEvent(),
            );
        }

        $status = match ($notificationObject->getEvent()) {
            NotificationEventType::PAYMENT_SUCCEEDED => RemittanceWebhookDTO::STATUS_SUCCEEDED,
            NotificationEventType::PAYMENT_CANCELED => RemittanceWebhookDTO::STATUS_CANCELED,
            NotificationEventType::PAYMENT_WAITING_FOR_CAPTURE => RemittanceWebhookDTO::STATUS_WAITING,
            NotificationEventType::REFUND_SUCCEEDED => RemittanceWebhookDTO::STATUS_REFUND,
            default => throw new InvalidArgumentException(
                'Неподдерживаемое событие: '.$notificationObject->getEvent(),
            ),
        };
        if ($status === RemittanceWebhookDTO::STATUS_REFUND) {
            return new RemittanceWebhookDTO(
                paymentSystemId: $someData['paymentId'],
                status: $status,
                externalStatus: $someData['paymentStatus'],
                refundSystemId: $someData['refundId'],
            );
        } else {
            return new RemittanceWebhookDTO(
                paymentSystemId: $someData['paymentId'],
                status: $status,
                externalStatus: $someData['paymentStatus'],
            );
        }
    }

    public function refund(Remittance $remittance, Refund $refund): RemittanceRefundDTO
    {
        $result = $this->ykassa->createRefund($remittance, $refund);

        return new RemittanceRefundDTO(
            refundId: $result['id'],
            status: $result['status'],
            reason: $result['reason'],
            amount: $result['amount'],
        );
    }
}
