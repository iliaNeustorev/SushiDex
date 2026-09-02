<?php

namespace App\Http\Resources\Users;

use App\Http\Resources\Role\RolePublicResource;
use Carbon\Carbon;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

class UserAuthResource extends Data
{
    public function __construct(
        public int $id,
        public string $first_name,
        public ?string $last_name,
        public ?string $email,
        public Carbon $created_at,
        #[DataCollectionOf(RolePublicResource::class)]
        public DataCollection $roles,
    ) {}
}
