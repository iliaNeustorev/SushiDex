<?php

namespace App\Http\Requests\User\Profile;

use App\Http\RequestDTO\User\Profile\ChangeAvatarReqDTO;
use Illuminate\Foundation\Http\FormRequest;
use Spatie\LaravelData\WithData;

class ChangeAvatarRequest extends FormRequest
{
    use WithData;

    public function dataClass(): string
    {
        return ChangeAvatarReqDTO::class;
    }
}
