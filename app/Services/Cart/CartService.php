<?php

namespace App\Services\Cart;

use App\Http\Resources\Carts\CartPublicResource;
use App\Models\Cart;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Throwable;

class CartService
{
    public function put(User $user, Product $product, int $count): ?Cart
    {
        DB::beginTransaction();
        try {
            $item = Cart::query()
                ->whereBelongsTo($user)
                ->whereBelongsTo($product)
                ->lockForUpdate()
                ->first();
            if ($count === 0) {
                $item?->delete();

                DB::commit();

                return null;
            }
            if ($item) {
                $item->update(['count' => $count]);
            } else {
                $item = Cart::create([
                    'user_id' => $user->id,
                    'product_id' => $product->id,
                    'count' => $count,
                ]);
            }

            DB::commit();

            return $item;
        } catch (Throwable $e) {
            DB::rollBack();
            report($e);

            throw ValidationException::withMessages([
                'cart' => 'Не удалось обновить корзину. Попробуйте ещё раз.',
            ]);
        }
    }

    public function calculateTotalPrice(Collection $products): float
    {
        $totalPrice = $products->reduce(function (float $carry, Product $product) {
            return $carry + ((float) $product->price * $product->pivot->count);
        }, 0.0);

        return round($totalPrice, 2);
    }

    public function syncTemp(User $client, array $tempCart): array
    {
        try {
            DB::beginTransaction();

            $productsActual = Product::active()
                ->whereIn('id', array_column($tempCart, 'product_id'))
                ->get();
            $tempCartByProductId = collect($tempCart)->keyBy('product_id');
            $cartItems = Cart::whereBelongsTo($client)
                ->whereIn('product_id', $productsActual->modelKeys())
                ->lockForUpdate()
                ->get()
                ->keyBy('product_id');

            foreach ($productsActual as $product) {
                $tempCount = $tempCartByProductId->get($product->id)['count'];
                $cartItem = $cartItems->get($product->id);

                if ($cartItem) {
                    $cartItem->update([
                        'count' => max($cartItem->count, $tempCount),
                    ]);
                } else {
                    Cart::create([
                        'user_id' => $client->id,
                        'product_id' => $product->id,
                        'count' => $tempCount,
                    ]);
                }
            }

            $cart = $client->products()->get();
            DB::commit();

            return [
                'success' => true,
                'items' => CartPublicResource::collect($cart),
                'total_price' => $this->calculateTotalPrice($cart),
            ];
        } catch (Throwable $exception) {
            report($exception);
            DB::rollBack();

            return ['success' => false];
        }
    }
}
