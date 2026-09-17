<?php

namespace App\Http\Resources\Images;

use Spatie\LaravelData\Data;

class ImagePublicResource extends Data
{
    public function __construct(
        public int $id,
        public string $path,
    ) {
    }
}
