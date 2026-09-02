<?php

namespace App\Http\Requests\Admin;

use App\Models\Role;
use App\Rules\CheckModelIds;
use Illuminate\Foundation\Http\FormRequest;

class ChangedRole extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'roles' => ['required', 'array', 'min:1', new CheckModelIds(Role::class)],
        ];
    }

    public function messages(): array
    {
        return [
            'roles.required' => 'Выберите хотя бы одну роль',
        ];
    }
}
