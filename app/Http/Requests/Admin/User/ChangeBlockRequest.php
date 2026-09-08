<?php

namespace App\Http\Requests\Admin\User;

use App\Http\RequestDTO\User\Admin\UserChangeBlockDTO;
use Illuminate\Foundation\Http\FormRequest;
use Spatie\LaravelData\WithData;

class ChangeBlockRequest extends FormRequest
{
    use WithData;

    public function dataClass(): string
    {
        return UserChangeBlockDTO::class;
    }
}
