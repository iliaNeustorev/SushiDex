<?php

namespace App\Http\Requests\User\Profile;

use App\Http\RequestDTO\User\Profile\ProfileSaveReqDTO;
use Illuminate\Foundation\Http\FormRequest;
use Spatie\LaravelData\WithData;

class SaveRequest extends FormRequest
{
    use WithData;

    public function dataClass(): string
    {
        return ProfileSaveReqDTO::class;
    }
}
