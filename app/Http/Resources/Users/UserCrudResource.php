<?php

namespace App\Http\Resources\Users;

use App\Http\Resources\Role\RoleCrudResource;
use Carbon\Carbon;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

class UserCrudResource extends Data
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public int $id,
        public string $first_name,
        public ?string $last_name,
        public ?string $middle_name,
        public ?string $email,
        public Carbon $created_at,
        #[DataCollectionOf(RoleCrudResource::class)]
        public DataCollection $roles,
        public ?string $address,
        public bool $block,
        public ?string $phone,
    ) {}
}
