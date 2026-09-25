<?php

namespace App\Http\Controllers;

use App\Http\Requests\Cart\SaveRequest;
use App\Http\Resources\Carts\CartPublicDetailsResource;
use App\Http\Resources\Carts\CartPublicResource;
use App\Models\Product;
use App\Services\Cart\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CartController extends Controller
{
    public function __construct(
        private readonly CartService $cartService
    ) {
    }

    public function index(Request $request)
    {
        if ($request->user()) {
            $products = $request->user()
                ->products()
                ->with(['category', 'previewImage'])
                ->get();
            $cart = [
                'items' => CartPublicResource::collect($products),
                'total_price' => $this->cartService->calculateTotalPrice($products),
            ];

            $cartDetails = CartPublicDetailsResource::collect($products);
        }
        return Inertia::render(
            'Cart/Index',
            [
                'cart' => fn() => $cart ?? ['items' => [], 'total_price' => 0],
                'cartDetails' => fn() => $cartDetails ?? collect()
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
            ]
        ]);
    }

    public function destroy(Request $request)
    {
        $client = $request->user();
        $client->products()->sync([]);

        return redirect()->back();
    }
}
