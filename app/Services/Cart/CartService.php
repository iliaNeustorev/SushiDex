<?php

namespace App\Services\Cart;

use App\Models\Cart;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Throwable;

class CartService
{
    public function put(User $user, Product $product, int $count): Cart
    {
        DB::beginTransaction();

        try {
            $item = Cart::query()
                ->whereBelongsTo($user)
                ->whereBelongsTo($product)
                ->lockForUpdate()
                ->first();

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

    public function remove(User $user, Cart $item): void
    {
        abort_unless($item->user_id === $user->id, 403);
        $item->delete();
    }
}
