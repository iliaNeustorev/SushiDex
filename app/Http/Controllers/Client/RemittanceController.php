<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\Remittance\RemittanceService;
use InvalidArgumentException;
use Log;

class RemittanceController extends Controller
{
    public function __construct(
        private readonly RemittanceService $remittanceService
    ) {}

    public function store(Order $order)
    {
        $confirmationUrl = $this->remittanceService->store($order);

        return response()->json(['url' => $confirmationUrl]);
    }

    public function callback()
    {
        $source = file_get_contents('php://input');
        $requestBody = json_decode($source, true);
        try {
            $this->remittanceService->handleWebhook($requestBody);

            return response('', 200);
        } catch (InvalidArgumentException $e) {
            Log::error($requestBody);
            report($e);

            return response('', 200);
        } catch (\Exception $e) {
            Log::error($requestBody);
            report($e);

            return response('', 500);
        }
    }
}
