<?php

namespace App\Enums\Orders;

enum TypePaid: int
{
    case CARD_ONLINE = 1;
    case CARD_COURIER = 2;
    case CASH_COURIER = 3;
    case IN_PICKUP_LOCATION = 4;

    public const TEXTS = [
        1 => 'Онлайн',
        2 => 'Картой курьеру',
        3 => 'Наличными курьеру',
        4 => 'В пункте выдачи',
    ];

    public function text(): string
    {
        return self::TEXTS[$this->value];
    }
}
