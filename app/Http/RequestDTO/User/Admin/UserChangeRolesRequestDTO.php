<?php

namespace App\Http\RequestDTO\User\Admin;

use App\Models\Role;
use App\Rules\CheckModelIds;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Rule;
use Spatie\LaravelData\Data;

class UserChangeRolesRequestDTO extends Data
{
    public function __construct(
        #[Min(1), Rule(new CheckModelIds(Role::class))]
        public array $roleIds
    ) {
    }
}
