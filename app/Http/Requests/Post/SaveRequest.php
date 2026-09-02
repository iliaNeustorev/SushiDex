<?php

namespace App\Http\Requests\Post;

use App\Http\RequestDTO\Posts\Admin\PostsSaveReqDTO;
use Illuminate\Foundation\Http\FormRequest;
use Spatie\LaravelData\WithData;

class SaveRequest extends FormRequest
{
    use WithData;

    protected function dataClass(): string
    {
        return PostsSaveReqDTO::class;
    }
}
