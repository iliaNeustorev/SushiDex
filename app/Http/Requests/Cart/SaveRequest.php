<?php

namespace App\Http\Requests\Cart;

use App\Http\RequestDTO\Carts\CartSaveReqDTO;
use Illuminate\Foundation\Http\FormRequest;
use Spatie\LaravelData\WithData;

class SaveRequest extends FormRequest
{
    use WithData;

    public function dataClass(): string
    {
        return CartSaveReqDTO::class;
    }
}
