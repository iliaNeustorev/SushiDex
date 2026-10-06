<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Orders\Status;
use App\Enums\Orders\TypePaid;
use App\Http\Controllers\Controller;
use App\Http\RequestDTO\Orders\Admin\OrdersActualQuery;
use App\Http\Requests\Order\UpdateRequest;
use App\Http\Requests\Order\UpdateSettingsRequest;
use App\Http\Resources\General\GeneralPagination;
use App\Http\Resources\Orders\Admin\OrderAdminPublicResource;
use App\Models\Order;
use App\Services\Order\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class OrderController extends Controller
{
    public function __construct(
        private readonly OrderService $orderService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Admin/Orders/Index', []);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, Order $order)
    {
        Gate::authorize('update', $order);
        $status = $request->getData()->status;
        $this->orderService->updateStatus($order, $status);

        return redirect()->back();
    }

    public function actual(Request $request)
    {
        $query = OrdersActualQuery::validateAndCreate($request->query())->toArray();
        $actualOrdersPaginator = $this->orderService->getOrdersWithPaginate(
            $query,
            [Status::NEW],
            'actualPage',
            'actualBatch',
        );
        $processingOrdersPaginator = $this->orderService->getOrdersWithPaginate(
            $query,
            [Status::PROCESSING],
            'processingPage',
            'processingBatch',
        );
        $actualOrders = GeneralPagination::from($actualOrdersPaginator, OrderAdminPublicResource::class);
        $processingOrders = GeneralPagination::from($processingOrdersPaginator, OrderAdminPublicResource::class);
        $typePaid = collect(TypePaid::TEXTS);

        return Inertia::render(
            'Admin/Orders/Actual',
            compact(
                'actualOrders',
                'processingOrders',
                'query',
                'typePaid'
            )
        );
    }

    public function updateSettings(UpdateSettingsRequest $request, Order $order)
    {
        Gate::authorize('view', $order);
        $data = $request->getData()->toArray();
        $this->orderService->updateSettings($order, $data);

        return redirect()->back();
    }
}
