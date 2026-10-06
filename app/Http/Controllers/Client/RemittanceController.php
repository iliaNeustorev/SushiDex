<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\Remittance\RemittanceService;
use Inertia\Inertia;

class RemittanceController extends Controller
{
    public function __construct(
        private readonly RemittanceService $remittanceService
    ) {}

    public function store(Order $order)
    {
        $confirmationUrl = $this->remittanceService->store($order);

        return Inertia::location($confirmationUrl);
    }
}
