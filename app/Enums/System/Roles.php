<?php

namespace App\Enums\System;

enum Roles: string
{
    case USER = 'user';
    case AUTHOR = 'author';
    case ADMIN = 'admin';
    case DEVELOPER = 'dev';

    public const TEXTS = [
        self::USER->value => 'Пользователь',
        self::AUTHOR->value => 'Автор',
        self::ADMIN->value => 'Admin',
        self::DEVELOPER->value => 'Разработчик',
    ];

    public function text(): string
    {
        return self::TEXTS[$this->value];
    }
}
