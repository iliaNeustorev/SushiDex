<?php

namespace App\Interfaces;

interface SmsSendInterface
{
    public function sendCode(string $phone, string $code): bool;
}
