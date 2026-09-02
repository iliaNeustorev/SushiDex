<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class SoftExists implements ValidationRule
{
    public function __construct(
        protected string $modelName
    ) {}

    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $id, Closure $fail): void
    {
        $record = $this->modelName::find($id);

        if ($record === null) {
            $fail("$attribute value is incorrect");
        }
    }
}
