<?php

namespace App\Http\Requests\Admin\User;

use App\Http\RequestDTO\User\Admin\UserChangeRolesRequestDTO;
use Spatie\LaravelData\Data;

class ChangeRolesRequest extends Data
{
    public function dataClass(): string
    {
        return UserChangeRolesRequestDTO::class;
    }
}
