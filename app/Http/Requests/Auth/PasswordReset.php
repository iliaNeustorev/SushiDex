<?php

namespace App\Http\Requests\Auth;

use App\Http\RequestDTO\Auth\ResetPasswordReqDTO;
use Illuminate\Foundation\Http\FormRequest;
use Spatie\LaravelData\WithData;

class PasswordReset extends FormRequest
{
    use WithData;

    public function dataClass(): string
    {
        return ResetPasswordReqDTO::class;
    }
}
