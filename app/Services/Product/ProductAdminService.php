<?php

namespace App\Services\Product;

use App\Models\Product;
use App\Services\Image\Service as ImageService;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class ProductAdminService
{
    public function __construct(private ImageService $imageService) {}

    public function delete(Product $product): void
    {
        $product->delete();
        $this->imageService->deleteAllImagesModel($product, ['catalogPath' => 'products']);
    }

    public function getProductsWithPaginate(array $query): LengthAwarePaginator
    {
        return QueryBuilder::for(Product::class)
            ->with('category')
            ->allowedFilters([
                'title',
                AllowedFilter::exact('category_id'),
                AllowedFilter::callback('date_from', fn ($query, $value) => $query->where('created_at', '>=', $value)),
                AllowedFilter::callback('date_to', fn ($query, $value) => $query->where('created_at', '<=', $value.' 23:59:59')),
            ])
            ->defaultSort('-id')
            ->allowedSorts(['id', 'title', 'price', 'created_at'])
            ->paginate($query['batch'] ?? 10);
    }

    public function getTrashedProductsWithPaginate(array $query): LengthAwarePaginator
    {
        return QueryBuilder::for(Product::onlyTrashed())
            ->with('category')
            ->allowedFilters([
                'title',
                AllowedFilter::callback('date_from', fn ($query, $value) => $query->where('created_at', '>=', $value)),
                AllowedFilter::callback('date_to', fn ($query, $value) => $query->where('created_at', '<=', $value.' 23:59:59')),
            ])
            ->defaultSort('-id')
            ->allowedSorts(['id', 'title', 'created_at'])
            ->paginate($query['batch'] ?? 10);
    }
}
