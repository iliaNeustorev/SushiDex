<?php

namespace App\Http\Requests\Images;

use App\Http\RequestDTO\Images\ImagesUploadReqDTO;
use Illuminate\Foundation\Http\FormRequest;
use Spatie\LaravelData\WithData;

class UploadRequest extends FormRequest
{
    use WithData;

    protected function dataClass(): string
    {
        return ImagesUploadReqDTO::class;
    }
}
