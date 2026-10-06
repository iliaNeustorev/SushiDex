<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\SaveRequest;
use App\Http\Resources\Orders\Client\OrderPublicResource;
use App\Http\Resources\Products\ProductOrderItemResource;
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
    public function index(Request $request)
    {
        $client = $request->user();

        return Inertia::render('Order/Index', [
            'orders' => fn () => OrderPublicResource::collect(
                Order::with('remittance')->withCount('items')->actualOrder($client->id)->get()
            ),
        ]);
    }

    public function show(Order $order)
    {
        Gate::authorize('view', $order);
        $order->load('products.previewImage', 'remittance')->loadCount('items');
        $products = ProductOrderItemResource::collect($order->products);
        $order = OrderPublicResource::from($order);

        return Inertia::render('Order/Show', compact('order', 'products'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SaveRequest $request)
    {
        $client = $request->user();
        $data = $request->getData()->toArray();
        $order = $this->orderService->create($client, $data);

        return redirect()->route('orders.show', $order->id);
    }
}
