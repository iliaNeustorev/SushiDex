<?php

namespace App\Http\Requests\PendingPhone;

use App\Http\RequestDTO\User\Phone\SendCodeReqDTO;
use Spatie\LaravelData\WithData;

class SendCodeRequest extends PendingPhoneRequest
{
    use WithData;

    public function dataClass(): string
    {
        return SendCodeReqDTO::class;
    }
}
