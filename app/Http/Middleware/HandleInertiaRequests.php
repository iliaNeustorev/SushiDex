<?php

namespace App\Http\Middleware;

use App\Http\Resources\Carts\CartPublicResource;
use App\Http\Resources\Users\UserAuthResource;
use App\Models\Cart;
use App\Services\Cart\CartService;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    public function __construct(
        private readonly CartService $cartService,
    ) {
    }

    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user();
        return array_merge(parent::share($request), [
            'user' => fn() => $user
                ? UserAuthResource::from($user->load('roles', 'image'))
                : null,
            'cart' => function () use ($user): array {
                if (!$user) {
                    return [
                        'items' => [],
                        'total_price' => 0.0,
                    ];
                }

                $products = $user->products()->get();

                return [
                    'items' => CartPublicResource::collect($products),
                    'total_price' => $this->cartService->calculateTotalPrice($products),
                ];
            },
        ]);
    }
}
