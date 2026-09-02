<?php

namespace App\Enums\Orders;

enum Status: int
{
    case NEW = 1;
    case PAID = 2;
    case PROCESSING = 3;
    case COMPLETED = 4;
    case CANCELLED = 5;

    public const TEXTS = [
        1 => 'Новый',
        2 => 'Оплачен',
        3 => 'В обработке',
        4 => 'Завершён',
        5 => 'Отменён',
    ];

    public function text(): string
    {
        return self::TEXTS[$this->value];
    }
}
