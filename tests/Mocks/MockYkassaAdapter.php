<?php

namespace Tests\Mocks;

use App\DTO\Remittances\RemittancePaymentDTO;
use App\DTO\Remittances\RemittanceRefundDTO;
use App\DTO\Remittances\RemittanceWebhookDTO;
use App\Interfaces\PaymentProviderInterface;
use App\Models\Order;
use App\Models\Refund;
use App\Models\Remittance;
use InvalidArgumentException;
use YooKassa\Model\Notification\NotificationEventType;

class MockYkassaAdapter implements PaymentProviderInterface
{
    public function store(Remittance $remittance, Order $order): RemittancePaymentDTO
    {
        return new RemittancePaymentDTO(
            paymentId: $this->paymentId($remittance),
            status: 'pending',
            amount: $remittance->amount,
            url: route('orders.show', $order),
        );
    }

    public function handleWebhook(array $payload): RemittanceWebhookDTO
    {
        $event = $payload['event'] ?? null;
        $object = $payload['object'] ?? null;

        if (($payload['type'] ?? null) !== 'notification' || ! is_string($event) || ! is_array($object)) {
            throw new InvalidArgumentException('Некорректное уведомление YooKassa.');
        }

        $status = match ($event) {
            NotificationEventType::PAYMENT_SUCCEEDED => RemittanceWebhookDTO::STATUS_SUCCEEDED,
            NotificationEventType::PAYMENT_WAITING_FOR_CAPTURE => RemittanceWebhookDTO::STATUS_WAITING,
            NotificationEventType::PAYMENT_CANCELED => RemittanceWebhookDTO::STATUS_CANCELED,
            NotificationEventType::REFUND_SUCCEEDED => RemittanceWebhookDTO::STATUS_REFUND,
            default => throw new InvalidArgumentException("Неподдерживаемое событие: {$event}"),
        };

        $paymentSystemId = $event === NotificationEventType::REFUND_SUCCEEDED
            ? ($object['payment_id'] ?? null)
            : ($object['id'] ?? null);
        $externalStatus = $object['status'] ?? null;

        if (! is_string($paymentSystemId) || ! is_string($externalStatus)) {
            throw new InvalidArgumentException('В уведомлении YooKassa отсутствуют обязательные данные объекта.');
        }

        $refundSystemId = null;

        if ($event === NotificationEventType::REFUND_SUCCEEDED) {
            $refundSystemId = $object['id'] ?? null;

            if (! is_string($refundSystemId)) {
                throw new InvalidArgumentException('В уведомлении YooKassa отсутствует идентификатор возврата.');
            }
        }

        return new RemittanceWebhookDTO(
            paymentSystemId: $paymentSystemId,
            status: $status,
            externalStatus: $externalStatus,
            refundSystemId: $refundSystemId,
        );
    }

    public function refund(Remittance $remittance, Refund $refund): RemittanceRefundDTO
    {
        return new RemittanceRefundDTO(
            refundId: $this->refundId($refund),
            status: 'succeeded',
            reason: $refund->reason ?? 'Возврат средств за заказ',
            amount: $refund->amount ?? $remittance->amount,
        );
    }

    private function paymentId(Remittance $remittance): string
    {
        return sprintf('23d93cac-000f-5000-8000-%012d', $remittance->getKey());
    }

    private function refundId(Refund $refund): string
    {
        return sprintf('216749f7-0016-50be-b000-%012d', $refund->getKey());
    }
}
