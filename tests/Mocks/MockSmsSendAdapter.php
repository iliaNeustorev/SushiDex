<?php

namespace Tests\Mocks;

use App\Interfaces\SmsSendInterface;
use Illuminate\Support\Facades\Log;

class MockSmsSendAdapter implements SmsSendInterface
{
    public function sendCode(string $phone, string $code): bool
    {
        Log::info("Смс c кодом $code отправлен на номер $phone");

        return true;
    }
}
