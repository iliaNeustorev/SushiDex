<?php

namespace App\Http\Resources\Tags;

use Carbon\Carbon;
use Spatie\LaravelData\Data;

class TagCrudResource extends Data
{
    public function __construct(
        public int $id,
        public string $url,
        public string $title,
        public string $description,
        public Carbon $created_at,
    ) {}
}
