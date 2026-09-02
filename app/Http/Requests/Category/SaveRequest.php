<?php

namespace App\Http\Requests\Category;

use App\Http\RequestDTO\Category\Admin\CategoriesSaveReqDTO;
use Illuminate\Foundation\Http\FormRequest;
use Spatie\LaravelData\WithData;

class SaveRequest extends FormRequest
{
    use WithData;

    protected function dataClass(): string
    {
        return CategoriesSaveReqDTO::class;
    }
}
