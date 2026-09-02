<?php

namespace App\Enums\Categories;

enum Type: int
{
    case PRODUCT = 1;
    case BLOG = 2;

    public const TEXTS = [
        1 => 'Продукт',
        2 => 'Блог',
    ];

    public function text(): string
    {
        return self::TEXTS[$this->value];
    }
}
