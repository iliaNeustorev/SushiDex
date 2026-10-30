<?php

namespace App\Http\Requests\User\Profile;

use App\Http\RequestDTO\User\Profile\GetAutocompleteAddressReqDTO;
use Illuminate\Foundation\Http\FormRequest;
use Spatie\LaravelData\WithData;

class AutocompleteAddressRequest extends FormRequest
{
    use WithData;

    public function dataClass(): string
    {
        return GetAutocompleteAddressReqDTO::class;
    }
}
