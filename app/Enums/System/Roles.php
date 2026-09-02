<?php

namespace App\Enums\System;

enum Roles: string
{
    case USER = 'user';
    case AUTHOR = 'author';
    case ADMIN = 'admin';
    case DEVELOPER = 'dev';
}
