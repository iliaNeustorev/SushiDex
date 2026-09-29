<?php

namespace App\Http\Requests\Cart;

use App\Http\RequestDTO\Carts\CartSyncTempReqDTO;
use Illuminate\Foundation\Http\FormRequest;
use Spatie\LaravelData\WithData;

class SyncTempRequest extends FormRequest
{
    use WithData;

    public function dataClass(): string
    {
        return CartSyncTempReqDTO::class;
    }
}
