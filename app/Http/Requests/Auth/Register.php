<?php

namespace App\Http\Requests\Auth;

use App\Http\RequestDTO\Auth\RegisterReqDTO;
use Illuminate\Foundation\Http\FormRequest;
use Spatie\LaravelData\WithData;

class Register extends FormRequest
{
    use WithData;

    public function dataClass(): string
    {
        return RegisterReqDTO::class;
    }
}
