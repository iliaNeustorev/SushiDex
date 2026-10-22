<?php

namespace App\Http\Requests\Notifications;

use App\Http\RequestDTO\Notifications\NotificationsQuery;
use Illuminate\Foundation\Http\FormRequest;
use Spatie\LaravelData\WithData;

class IndexRequest extends FormRequest
{
    use WithData;

    public function dataClass(): string
    {
        return NotificationsQuery::class;
    }
}
