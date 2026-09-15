<?php

namespace App\Http\Requests\PendingPhone;

use App\Models\PendingPhone;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

abstract class PendingPhoneRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'id' => [
                'required',
                'integer',
                Rule::exists(PendingPhone::class, 'id')
                    ->where('user_id', auth()->id()),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'id.required' => 'Не выбран номер телефона.',
            'id.integer' => 'Некорректный идентификатор телефона.',
            'id.exists' => 'Номер не найден или не принадлежит вашему аккаунту.',
        ];
    }
}
