<?php

namespace App\Http\Resources\Users;

use App\Http\Resources\Phone\Client\PendingPhoneProfileResource;
use App\Http\Resources\Phone\Client\PhoneProfileResource;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

class UserProfileResource extends Data
{
    public function __construct(
        public int $id,
        public string $first_name,
        public ?string $last_name,
        public ?string $middle_name,
        public ?string $email,
        public ?string $address,
        public ?PhoneProfileResource $phone,
        #[DataCollectionOf(PendingPhoneProfileResource::class)]
        public DataCollection $pendingPhones,
    ) {}
}
