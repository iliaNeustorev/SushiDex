<?php

namespace App\Http\Resources\Tags;

use Spatie\LaravelData\Data;

class TagPublicResource extends Data
{
    public function __construct(
        public string $url,
        public string $title,
        public ?string $description,
    ) {}
}
