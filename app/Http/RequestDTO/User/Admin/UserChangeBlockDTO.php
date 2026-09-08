<?php

namespace App\Http\RequestDTO\User\Admin;

use Spatie\LaravelData\Data;

class UserChangeBlockDTO extends Data
{
    public function __construct(

        public bool $block
    ) {}
}
