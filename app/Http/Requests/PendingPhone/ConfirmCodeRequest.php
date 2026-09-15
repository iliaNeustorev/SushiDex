<?php

namespace App\Http\Requests\PendingPhone;

use App\Http\RequestDTO\User\Phone\ConfirmCodeReqDTO;
use Spatie\LaravelData\WithData;

class ConfirmCodeRequest extends PendingPhoneRequest
{
    use WithData;

    public function dataClass(): string
    {
        return ConfirmCodeReqDTO::class;
    }

    public function rules(): array
    {
        return [
            ...parent::rules(),
            'code' => ['required', 'digits:6'],
        ];
    }

    public function messages(): array
    {
        return [
            ...parent::messages(),
            'code.digits' => 'Код должен состоять из 6 симолов',
        ];
    }
}
