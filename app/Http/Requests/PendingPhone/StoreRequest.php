<?php

namespace App\Http\Requests\PendingPhone;

use App\Http\RequestDTO\User\Phone\AddPhoneReqDTO;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\WithData;

class StoreRequest extends FormRequest
{
    use withData;

    public function dataClass(): string
    {
        return AddPhoneReqDTO::class;
    }

    public function rules(): array
    {
        return [
            'phone' => [Rule::unique('pending_phones', 'phone')->where('user_id', $this->user()->id)]
        ];
    }

    public function messages(): array
    {
        return [
            'phone.unique' => 'Этот телефон уже добавлен в ваш аккаунт',
        ];
    }
}
