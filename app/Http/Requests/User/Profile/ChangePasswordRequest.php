<?php

namespace App\Http\Requests\User\Profile;

use App\Http\RequestDTO\User\Profile\ChangePasswordReqDTO;
use Illuminate\Foundation\Http\FormRequest;
use Spatie\LaravelData\WithData;

class ChangePasswordRequest extends FormRequest
{
    use WithData;

    public function dataClass(): string
    {
        return ChangePasswordReqDTO::class;
    }
}
