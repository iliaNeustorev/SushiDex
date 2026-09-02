<?php

namespace App\Services\Category;

use App\Models\Category;
use App\Services\Image\Service as ImageService;
use Exception;

class CategoryAdminService
{
    public function __construct(
        protected ImageService $imageService
    ) {}

    /**
     * @throws Exception
     */
    public function delete(Category $category): bool
    {
        if ($category->posts()->exists() || $category->products()->exists()) {
            return false;
        }
        $category->delete();
        $this->imageService->deleteAllImagesModel($category, ['catalogPath' => 'categories']);

        return true;
    }
}
