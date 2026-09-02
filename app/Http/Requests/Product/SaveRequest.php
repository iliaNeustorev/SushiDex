<?php

namespace App\Http\Requests\Product;

use App\Http\RequestDTO\Product\Admin\ProductsSaveReqDTO;
use Illuminate\Foundation\Http\FormRequest;
use Spatie\LaravelData\WithData;

class SaveRequest extends FormRequest
{
    use WithData;

    public function dataClass(): string
    {
        return ProductsSaveReqDTO::class;
    }
}
