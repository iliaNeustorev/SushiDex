<?php

namespace App\Enums\Posts;

enum Status: int
{
    case DRAFT = 0;
    case MODERATING = 5;
    case PUBLISHED = 10;
    case REJECTED = 15;

    public const TEXTS = [
        0 => 'Черновик',
        5 => 'На модерации',
        10 => 'Опубликован',
        15 => 'Отклонен модератором',
    ];

    public function text(): string
    {
        return self::TEXTS[$this->value];
    }
}
