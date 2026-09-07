<?php

namespace App\Http\Resources\Role;

use App\Enums\System\Roles;
use Spatie\LaravelData\Data;

class RoleCrudResource extends Data
{
    public function __construct(
        public int $id,
        public Roles $name,
        public string $description,
    ) {
    }
}
