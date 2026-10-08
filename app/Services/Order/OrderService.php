<?php

namespace App\Services\Order;

use App\Enums\Orders\Status as OrderStatus;
use App\Enums\Orders\TypePaid;
use App\Enums\Remittances\Status as RemittanceStatus;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use Throwable;

class OrderService
{
    public function create(User $user, array $data): Order
    {
        DB::beginTransaction();

        try {
            $actualCountOrders = Order::where('user_id', $user->id)->actualStatus()->count();
            if ($actualCountOrders >= Order::LIMIT_ACTUAL_ORDERS) {
                throw ValidationException::withMessages(['order' => 'Нельзя создать заказ у вас уже 2 активных заказа!']);
            }

            $this->validateDeliverySettings($user, $data);

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

            $total = $items->sum(fn (Cart $item) => $item->product->price * $item->count);
            $order = Order::create([
                'user_id' => $user->id,
                'total_price' => number_format($total, 2, '.', ''),
                'status' => OrderStatus::NEW,
                'type_paid' => $data['type'],
                'need_delivery' => $data['need_delivery'],
            ]);

            foreach ($items as $cartItem) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cartItem->product->id,
                    'count' => $cartItem->count,
                    'price' => $cartItem->product->price,
                ]);
            }

            if ($order->type_paid === TypePaid::CARD_ONLINE) {
                $order->remittance()->create([
                    'amount' => $order->total_price,
                    'status' => RemittanceStatus::AWAIT_PAID,
                ]);
            }

            Cart::query()->whereKey($items->modelKeys())->delete();
            $order->load('items.product');

            DB::commit();

            // TODO: добавить уведовмление админу и клиенту о успешном заказе
            return $order;
        } catch (ValidationException $e) {
            DB::rollBack();

            throw $e;
        } catch (Throwable $e) {
            DB::rollBack();
            report($e);
            // TODO: добавить уведовмление админу и клиенту о неуспешном заказе
            throw ValidationException::withMessages([
                'order' => 'Не удалось создать заказ. Попробуйте ещё раз.',
            ]);
        }
    }

    public function getOrdersWithPaginate(
        array $data,
        array $statuses,
        string $pageName,
        string $batchName,
    ): LengthAwarePaginator {
        return QueryBuilder::for(Order::byStatus($statuses))
            ->with('products', 'user.phone', 'remittance')
            ->allowedFilters([
                AllowedFilter::exact('id'),
            ])
            ->defaultSort('-id')
            ->paginate(
                perPage: $data[$batchName] ?? 10,
                pageName: $pageName,
                page: $data[$pageName] ?? 1,
            );
    }

    public function updateStatus(Order $order, OrderStatus $status): void
    {
        switch ($status) {
            case OrderStatus::PROCESSING:
                if (in_array($order->status, [OrderStatus::NEW])) {
                    if ($order->type_paid === TypePaid::CARD_ONLINE) {
                        $order->loadMissing('remittance');
                        if (! $order->remittance || $order->remittance->status === RemittanceStatus::AWAIT_PAID) {
                            throw ValidationException::withMessages([
                                'order' => 'Не удалось обновить статус заказа. Ожидается оплата заказа онлайн',
                            ]);
                        }
                    }
                    $order->update(['status' => OrderStatus::PROCESSING]);

                    // TODO: отправить уведомление
                    return;
                }
                break;
            case OrderStatus::COMPLETED:
                if ($order->status === OrderStatus::PROCESSING) {
                    $order->update([
                        'status' => OrderStatus::COMPLETED,
                        'completed_at' => now(),
                    ]);

                    // TODO: отправить уведомление
                    return;
                }
                break;
            case OrderStatus::CANCELLED:
                if (in_array($order->status, [OrderStatus::NEW, OrderStatus::PROCESSING])) {
                    $order->loadMissing('remittance');
                    if ($order->type_paid === TypePaid::CARD_ONLINE && $order->remittance?->paid) {
                        // TODO: если оплата была картой онлайн то сделать возврат
                    }
                    $order->update([
                        'status' => OrderStatus::CANCELLED,
                        'completed_at' => now(),
                    ]);

                    // TODO: отправить уведомление
                    return;
                }
                break;
        }
        throw ValidationException::withMessages([
            'order' => 'Не удалось обновить статус заказа. Попробуйте ещё раз или обратитесь к администратору.',
        ]);
    }

    public function updateSettings(Order $order, array $settings): void
    {
        DB::beginTransaction();
        try {
            $order->loadMissing('user');
            $needDelivery = $settings['need_delivery'] ?? $order->need_delivery;
            $type = $settings['type'] ?? $order->type_paid->value;

            if ($needDelivery) {
                if (! $this->hasDeliveryAddress($order->user)) {
                    throw ValidationException::withMessages([
                        'need_delivery' => 'Не удалось включить доставку. Не указан адрес в профиле.',
                    ]);
                }
            }
            if (! $this->hasDeliveryAddress($order->user) && in_array($type, [TypePaid::CARD_COURIER->value, TypePaid::CASH_COURIER->value], true)) {
                throw ValidationException::withMessages([
                    'type' => 'Нельзя выбрать данный тип. Не указан адрес в профиле.',
                ]);
            }
            if (isset($settings['type']) && $type === TypePaid::CARD_ONLINE->value && $order->type_paid !== TypePaid::CARD_ONLINE) {
                $order->remittance()->firstOrCreate([], [
                    'amount' => $order->total_price,
                    'status' => RemittanceStatus::AWAIT_PAID,
                ]);
            }
            if (isset($settings['type']) && $type !== TypePaid::CARD_ONLINE->value && $order->type_paid === TypePaid::CARD_ONLINE) {
                $order->loadMissing('remittance');
                if (isset($order->remittance) && ($order->remittance->paid || $order->remittance->status === RemittanceStatus::AWAIT_CONFIRM_PAID)) {
                    throw ValidationException::withMessages([
                        'type' => 'Не удалось обновить тип оплаты. Заказ уже оплачен или ожидает подтверждения оплаты.',
                    ]);
                } else {
                    $order->remittance()->delete();
                }
            }

            $order->update([
                'type_paid' => $type,
                'need_delivery' => $needDelivery,
            ]);

            // TODO: отправить уведомление

            DB::commit();
        } catch (ValidationException $e) {
            DB::rollBack();

            throw $e;
        } catch (Throwable $e) {
            DB::rollBack();
            report($e);

            throw ValidationException::withMessages([
                'order' => 'Не удалось изменить настройки заказа. Попробуйте ещё раз.',
            ]);
        }
    }

    public function getTypePaid(User $user): Collection
    {
        $collect = collect(TypePaid::TEXTS);
        if (! $this->hasDeliveryAddress($user)) {
            $collect = $collect->filter(fn ($type, $key) => in_array($key, [TypePaid::CARD_ONLINE->value, TypePaid::IN_PICKUP_LOCATION->value]));
        }

        return $collect;
    }

    private function validateDeliverySettings(User $user, array $data): void
    {
        if (($data['need_delivery'] ?? false) && ! $this->hasDeliveryAddress($user)) {
            throw ValidationException::withMessages([
                'need_delivery' => 'Не удалось включить доставку. Не указан адрес в профиле.',
            ]);
        }

        $type = $data['type'] instanceof TypePaid
            ? $data['type']->value
            : $data['type'];

        if (! $this->hasDeliveryAddress($user) && in_array($type, [TypePaid::CARD_COURIER->value, TypePaid::CASH_COURIER->value], true)) {
            throw ValidationException::withMessages([
                'type' => 'Нельзя выбрать данный тип. Не указан адрес в профиле.',
            ]);
        }
    }

    private function hasDeliveryAddress(User $user): bool
    {
        return is_string($user->address) && trim($user->address) !== '';
    }

    public function getCompletedWithPaginate(array $query): LengthAwarePaginator
    {
        return QueryBuilder::for(Order::completedStatus())
            ->with('products', 'remittance', 'user')
            ->allowedFilters([
                AllowedFilter::exact('id'),
                AllowedFilter::exact('status'),
                AllowedFilter::exact('type_paid'),
                AllowedFilter::callback('date_created_from', fn ($q, $v) => $q->where('created_at', '>=', $v)),
                AllowedFilter::callback('date_created_to', fn ($q, $v) => $q->where('created_at', '<=', $v.' 23:59:59')),
                AllowedFilter::callback('date_completed_from', fn ($q, $v) => $q->where('completed_at', '>=', $v)),
                AllowedFilter::callback('date_completed_to', fn ($q, $v) => $q->where('completed_at', '<=', $v.' 23:59:59')),
            ])
            ->allowedSorts([
                'id',
                'created_at',
                'completed_at',
                'status',
                'type_paid',
                'total_price',
            ])
            ->defaultSort('-id')
            ->paginate($query['batch'] ?? 10);
    }
}
