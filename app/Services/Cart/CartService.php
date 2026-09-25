<?php

namespace App\Services\Cart;

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
            return $carry + ((float)$product->price * $product->pivot->count);
        }, 0.0);
        return round($totalPrice, 2);
    }
}
