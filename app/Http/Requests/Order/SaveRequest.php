<?php

namespace App\Http\Requests\Order;

use App\Http\RequestDTO\Orders\OrderSaveReqDTO;
use Illuminate\Foundation\Http\FormRequest;
use Spatie\LaravelData\WithData;

class SaveRequest extends FormRequest
{
    use WithData;

    public function dataClass(): string
    {
        return OrderSaveReqDTO::class;
    }
}
