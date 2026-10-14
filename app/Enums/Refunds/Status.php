<?php

namespace App\Enums\Refunds;

use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript('RefundStatus')]
enum Status: int
{
    case NEW = 1;
    case SUCCEED = 2;
    case CANCELED = 3;

    public const TEXTS = [
        1 => 'Новый',
        2 => 'Выполнен',
        3 => 'Отменен',
    ];

    public function text(): string
    {
        return self::TEXTS[$this->value];
    }
}
