<?php

namespace App\Http\Requests\Order;

use App\Http\RequestDTO\Orders\OrderUpdateSettingsReqDTO;
use Illuminate\Foundation\Http\FormRequest;
use Spatie\LaravelData\WithData;

class UpdateSettingsRequest extends FormRequest
{
    use WithData;

    public function dataClass(): string
    {
        return OrderUpdateSettingsReqDTO::class;
    }
}
