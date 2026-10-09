<?php

namespace App\Enums\Remittances;

use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript('RemittanceStatus')]
enum Status: int
{
    case PAID = 1;
    case AWAIT_PAID = 2;
    case REFUND = 3;

    public const TEXTS = [
        1 => 'Оплачен',
        2 => 'Ожидает оплаты',
        3 => 'Возврат',
    ];

    public function text(): string
    {
        return self::TEXTS[$this->value];
    }
}
