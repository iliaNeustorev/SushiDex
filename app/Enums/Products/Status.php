<?php

namespace App\Enums\Products;

enum Status: int
{
    case IN_CART = 1;
    case ORDERED = 2;

    public const TEXTS = [
        1 => 'Товар в корзине',
        2 => 'В заказе',
    ];

    public function text(): string
    {
        return self::TEXTS[$this->value];
    }
}
