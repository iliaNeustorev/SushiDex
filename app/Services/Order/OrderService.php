<?php

namespace App\Services\Order;

use App\Enums\Orders\Status as OrderStatus;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Throwable;

class OrderService
{
    public function checkout(User $user): Order
    {
        DB::beginTransaction();

        try {
            /** @var Collection<int, Cart> $items */
            $items = Cart::query()
                ->with('product')
                ->whereBelongsTo($user)
                ->lockForUpdate()
                ->get();

            if ($items->isEmpty()) {
                throw ValidationException::withMessages(['cart' => 'Корзина пуста.']);
            }

            if ($items->contains(fn (Cart $item) => ! $item->product)) {
                throw ValidationException::withMessages(['cart' => 'Один из товаров больше недоступен.']);
            }

            $total = $items->sum(fn (Cart $item) => (float) $item->product->price * $item->count);
            $order = Order::create([
                'user_id' => $user->id,
                'total_price' => number_format($total, 2, '.', ''),
                'status' => OrderStatus::NEW,
            ]);

            foreach ($items as $cartItem) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cartItem->product->id,
                    'count' => $cartItem->count,
                    'price' => $cartItem->product->price,
                ]);
            }

            Cart::query()->whereKey($items->modelKeys())->delete();
            $order->load('items.product');

            DB::commit();

            return $order;
        } catch (ValidationException $e) {
            DB::rollBack();

            throw $e;
        } catch (Throwable $e) {
            DB::rollBack();
            report($e);

            throw ValidationException::withMessages([
                'order' => 'Не удалось создать заказ. Попробуйте ещё раз.',
            ]);
        }
    }

    public function markPaid(Order $order): Order
    {
        DB::beginTransaction();

        try {
            $lockedOrder = Order::query()->lockForUpdate()->findOrFail($order->id);

            if ($lockedOrder->status !== OrderStatus::NEW) {
                throw ValidationException::withMessages(['order' => 'Заказ уже обработан.']);
            }

            $lockedOrder->update([
                'status' => OrderStatus::PAID,
            ]);
            $lockedOrder->refresh();

            DB::commit();

            return $lockedOrder;
        } catch (ValidationException $e) {
            DB::rollBack();

            throw $e;
        } catch (Throwable $e) {
            DB::rollBack();
            report($e);

            throw ValidationException::withMessages([
                'order' => 'Не удалось обновить заказ. Попробуйте ещё раз.',
            ]);
        }
    }
}
