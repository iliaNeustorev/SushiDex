<?php

namespace App\Integrations\Sms;

class SmsAero
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function sendSms(string $phoneNumber, string $message): true
    {
        return true;
    }
}
