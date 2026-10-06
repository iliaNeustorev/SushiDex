<?php

namespace App\Http\Requests\Order;

use App\Http\RequestDTO\Orders\Admin\OrderUpdateReqDTO;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Spatie\LaravelData\WithData;

class UpdateRequest extends FormRequest
{
    use WithData;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function dataClass(): string
    {
        return OrderUpdateReqDTO::class;
    }
}
