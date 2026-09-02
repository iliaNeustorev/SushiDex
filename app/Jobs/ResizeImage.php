<?php

namespace App\Jobs;

use App\Models\Image;
use App\Services\Image\Service as ImageService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ResizeImage implements ShouldQueue
{
    use Queueable;

    public $deleteWhenMissingModels = true;

    public $tries = 5;

    protected Image $image;

    protected array $data;

    /**
     * Create a new job instance.
     */
    public function __construct(Image $image, array $data = [])
    {
        $this->image = $image;
        $this->data = $data;
    }

    /**
     * Execute the job.
     */
    public function handle(ImageService $imageService): void
    {
        $resultResize = $imageService->resizeImage($this->image, $this->data);
        if (! $resultResize) {
            $this->release(3);
        }
    }
}
