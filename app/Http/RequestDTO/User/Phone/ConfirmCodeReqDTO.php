<?php

namespace App\Http\RequestDTO\User\Phone;

use Spatie\LaravelData\Attributes\MergeValidationRules;
use Spatie\LaravelData\Data;

#[MergeValidationRules]
class ConfirmCodeReqDTO extends Data
{
    public function __construct(
        public string $code,
        public int $id,
    ) {}
}
