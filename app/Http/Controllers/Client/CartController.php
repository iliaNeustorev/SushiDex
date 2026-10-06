<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cart\SaveRequest;
use App\Http\Requests\Cart\SyncTempRequest;
use App\Http\Resources\Carts\CartPublicDetailsResource;
use App\Http\Resources\Carts\CartPublicResource;
use App\Models\Product;
use App\Services\Cart\CartService;
use App\Services\Order\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CartController extends Controller
{
    public function __construct(
        private readonly CartService $cartService,
        private readonly OrderService $orderService,
    ) {}

    public function index(Request $request)
    {
        $client = $request->user();
        if ($client) {
            $products = $client
                ->products()
                ->with(['category', 'previewImage'])
                ->get();
            $cart = [
                'items' => CartPublicResource::collect($products),
                'total_price' => $this->cartService->calculateTotalPrice($products),
            ];

            $cartDetails = CartPublicDetailsResource::collect($products);
            $typePaid = $this->orderService->getTypePaid($client);
        }

        return Inertia::render(
            'Cart/Index',
            [
                'cart' => fn () => $cart ?? ['items' => [], 'total_price' => 0],
                'cartDetails' => fn () => $cartDetails ?? collect(),
                'typePaid' => fn () => $typePaid ?? collect(),
            ]
        );
    }

    public function update(SaveRequest $request): JsonResponse
    {
        $client = $request->user();
        $data = $request->getData()->toArray();
        $product = Product::active()->findOrFail($data['product_id']);
        $this->cartService->put($client, $product, $data['count']);
        $products = $client->products()->get();

        return response()->json([
            'cart' => [
                'items' => CartPublicResource::collect($products),
                'total_price' => $this->cartService->calculateTotalPrice($products),
            ],
        ]);
    }

    public function destroy(Request $request)
    {
        $client = $request->user();
        $client->products()->sync([]);

        return redirect()->back();
    }

    public function syncWithTemp(SyncTempRequest $request)
    {
        $cartTemp = $request->getData()->tempCart;
        $client = $request->user();
        $resultSync = $this->cartService->syncTemp($client, $cartTemp);
        if ($resultSync['success']) {
            return response()->json([
                'cart' => [
                    'items' => $resultSync['items'],
                    'total_price' => $resultSync['total_price'],
                ],
            ]);
        }

        return response()->json([], 400);
    }
}
