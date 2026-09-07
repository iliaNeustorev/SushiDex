<?php

namespace App\Http\Requests\Tag;

use App\Http\RequestDTO\Tags\Admin\TagsSaveReqDTO;
use Illuminate\Foundation\Http\FormRequest;
use Spatie\LaravelData\WithData;

class SaveRequest extends FormRequest
{
    use withData;

    public function dataClass(): string
    {
        return TagsSaveReqDTO::class;
    }
}
