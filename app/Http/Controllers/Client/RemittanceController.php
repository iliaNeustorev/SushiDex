<?php

namespace App\Http\Controllers\Client;

use App\Enums\Remittances\Status;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Remittance;
use App\Services\Remittance\RemittanceService;
use Carbon\Carbon;
use Inertia\Inertia;
use Log;
use YooKassa\Model\Notification\NotificationEventType;
use YooKassa\Model\Notification\NotificationSucceeded;
use YooKassa\Model\Notification\NotificationWaitingForCapture;

class RemittanceController extends Controller
{
    public function __construct(
        private readonly RemittanceService $remittanceService
    ) {
    }

    public function store(Order $order)
    {
        $confirmationUrl = $this->remittanceService->store($order);

        return Inertia::location($confirmationUrl);
    }

    public function callback()
    {
        $source = file_get_contents('php://input');
        $requestBody = json_decode($source, true);
        try {
            $notification = ($requestBody['event'] === NotificationEventType::PAYMENT_SUCCEEDED)
                ? new NotificationSucceeded($requestBody)
                : new NotificationWaitingForCapture($requestBody);
            $payment = $notification->getObject();
            $remittance = Remittance::where('payment_system_id', $payment->getId())->first();
            if ($remittance) {
                if (!$remittance->paid && $remittance->status !== Status::PAID) {
                    $remittance->update([
                        'paid' => true,
                        'payment_system_status' => NotificationEventType::PAYMENT_SUCCEEDED,
                        'payed_at' => Carbon::now(),
                        'status' => Status::PAID
                    ]);
                    Log::info($requestBody);
                    return response('', 200);
                }
            } else {
                throw new \Exception('Не найдена оплата id ' . $payment->getId());
            }
        } catch (\Exception $e) {
            Log::error($requestBody);
            report($e);
            return response('', 500);
        }
    }
}
