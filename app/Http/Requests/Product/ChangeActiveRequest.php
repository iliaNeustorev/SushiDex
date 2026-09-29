<?php

namespace App\Http\Requests\Product;

use App\Http\RequestDTO\Product\Admin\ProductsChangeActiveDTO;
use Illuminate\Foundation\Http\FormRequest;
use Spatie\LaravelData\WithData;

class ChangeActiveRequest extends FormRequest
{
    use WithData;

    public function dataClass(): string
    {
        return ProductsChangeActiveDTO::class;
    }
}
