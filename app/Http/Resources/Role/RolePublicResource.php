<?php

namespace App\Http\Resources\Role;

use App\Enums\System\Roles;
use Spatie\LaravelData\Data;

class RolePublicResource extends Data
{
    public function __construct(
        public Roles $name,
        public string $description,
    ) {}
}
