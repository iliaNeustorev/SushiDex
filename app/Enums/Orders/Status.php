<?php

namespace App\Enums\Orders;

use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript('OrderStatus')]
enum Status: int
{
    case NEW = 1;
    case PROCESSING = 2;
    case COMPLETED = 3;
    case CANCELLED = 4;

    public const TEXTS = [
        1 => 'Новый',
        2 => 'В обработке',
        3 => 'Завершён',
        4 => 'Отменён',
    ];

    public function text(): string
    {
        return self::TEXTS[$this->value];
    }
}
