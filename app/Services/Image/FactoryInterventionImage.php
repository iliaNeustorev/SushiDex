<?php

namespace App\Services\Image;

use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Drivers\Imagick\Driver as ImagickDriver;
use Intervention\Image\ImageManager;

class FactoryInterventionImage
{
    public static function make(string $driverType): ImageManager
    {
        return match ($driverType) {
            'gd' => new ImageManager(new Driver),
            'imagick' => new ImageManager(new ImagickDriver),
        };
    }
}
