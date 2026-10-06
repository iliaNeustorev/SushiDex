<?php

namespace App\Http\Requests\User\Profile;

use App\Http\RequestDTO\User\Profile\ChangeAddressReqDTO;
use Illuminate\Foundation\Http\FormRequest;
use Spatie\LaravelData\WithData;

class ChangeAddressRequest extends FormRequest
{
    use WithData;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function dataClass(): string
    {
        return ChangeAddressReqDTO::class;
    }
}
