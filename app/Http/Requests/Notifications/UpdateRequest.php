<?php

namespace App\Http\Requests\Notifications;

use App\Http\RequestDTO\Notifications\NotificationsUpdateDTO;
use Illuminate\Foundation\Http\FormRequest;
use Spatie\LaravelData\WithData;

class UpdateRequest extends FormRequest
{
    use WithData;

    public function dataClass(): string
    {
        return NotificationsUpdateDTO::class;
    }
}
