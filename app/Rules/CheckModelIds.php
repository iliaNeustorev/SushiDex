<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class CheckModelIds implements ValidationRule
{
    public function __construct(
        protected string $modelName
    ) {}

    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $ids, Closure $fail): void
    {
        $idsFromBase = $this->modelName::whereIn('id', $ids)->count();
        if ($idsFromBase !== count($ids)) {
            $fail("$attribute id in array is incorrect");
        }
    }
}
