<?php

namespace App\Integrations\Sms;

use App\Interfaces\SmsSendInterface;

readonly class SmsAeroAdapter implements SmsSendInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private SmsAero $smsAero
    ) {}

    public function sendCode(string $phone, string $code): bool
    {
        return $this->smsAero->sendSms($phone, $code);
    }
}
