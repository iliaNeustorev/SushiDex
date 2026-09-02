<?php

namespace App\Http\Resources\Images;

use Carbon\Carbon;
use Spatie\LaravelData\Data;

class ImageCrudResource extends Data
{
    public function __construct(
        public int $id,
        public string $path,
        public Carbon $created_at
    ) {}
}
