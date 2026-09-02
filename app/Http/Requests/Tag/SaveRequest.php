<?php

namespace App\Http\Requests\Tag;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $uniqueUrl = Rule::unique('tags');

        if (isset($this->tag)) {
            $uniqueUrl->ignoreModel($this->tag);
        }

        return [
            'url' => ['required', 'min:5', 'max:255', $uniqueUrl],
            'title' => ['required', 'min:5', 'max:255'],
            'description' => ['nullable'],
        ];
    }
}
