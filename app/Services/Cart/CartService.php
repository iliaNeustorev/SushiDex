<?php

namespace App\Services\Cart;

use App\Models\Cart;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CartService
{
    public function put(User $user, Product $product, int $count): Cart
    {
        return DB::transaction(function () use ($user, $product, $count) {
            $item = Cart::query()
                ->whereBelongsTo($user)
                ->whereBelongsTo($product)
                ->lockForUpdate()
                ->first();

            if ($item) {
                $item->update(['count' => $count]);

                return $item->refresh();
            }

            return Cart::create([
                'user_id' => $user->id,
                'product_id' => $product->id,
                'count' => $count,
            ]);
        });
    }

    public function remove(User $user, Cart $item): void
    {
        abort_unless($item->user_id === $user->id, 403);
        $item->delete();
    }
}
