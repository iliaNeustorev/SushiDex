<?php

namespace App\Services\Category;

use App\Models\Category;
use App\Services\Image\Service as ImageService;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

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

    public function getTrashedCategoriesWithPaginate(array $query): LengthAwarePaginator
    {
        return QueryBuilder::for(Category::onlyTrashed())
            ->allowedFilters([
                'title',
                AllowedFilter::callback('date_from', fn ($q, $v) => $q->where('created_at', '>=', $v)),
                AllowedFilter::callback('date_to', fn ($q, $v) => $q->where('created_at', '<=', $v.' 23:59:59')),
            ])
            ->defaultSort('-id')
            ->allowedSorts('id', 'title', 'created_at')
            ->paginate($query['batch'] ?? 10);
    }

    public function getCategoriesWithPaginate(array $query): LengthAwarePaginator
    {
        return QueryBuilder::for(Category::class)
            ->allowedFilters([
                'title',
                'url',
                AllowedFilter::exact('type'),
                AllowedFilter::callback('date_from', fn ($q, $v) => $q->where('created_at', '>=', $v)),
                AllowedFilter::callback('date_to', fn ($q, $v) => $q->where('created_at', '<=', $v.' 23:59:59')),
            ])
            ->defaultSort('-id')
            ->allowedSorts(['id', 'title', 'created_at', 'type'])
            ->paginate($query['batch'] ?? 10);
    }
}
