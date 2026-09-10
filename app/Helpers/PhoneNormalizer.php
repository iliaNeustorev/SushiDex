<?php

namespace App\Helpers;

final class PhoneNormalizer
{

    public static function normalize(string|int|null $value): ?string
    {
        if (is_null($value) || $value === '') {
            return null;
        }
        $phone = preg_replace('/\D+/', '', (string) $value);

        return preg_replace('/^8/', '7', $phone);
    }
}
