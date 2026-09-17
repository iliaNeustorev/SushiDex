<?php

namespace App\Services\Product;

use App\Enums\Categories\Type;
use App\Http\Resources\General\GeneralPagination;
use App\Http\Resources\Products\ProductPublicResource;
use App\Models\Category;
use App\Models\Product;
use Spatie\QueryBuilder\QueryBuilder;

class ProductClientService
{
    public function getProductWithPagination(array $filters): array
    {
        $categories = Category::byType(Type::PRODUCT)->orderBy('title')->get();
        $categoryUrl = $filters['url'] ?? $categories->first()?->url;
        $categoryId = $categories->where('url', $categoryUrl)->value('id');
        $productsPaginator = QueryBuilder::for(Product::class)
            ->with('images')
            ->allowedFilters('title')
            ->active()
            ->where('category_id', $categoryId)
            ->orderByDesc('id')
            ->paginate(6);
        $products = GeneralPagination::fromPaginator($productsPaginator, ProductPublicResource::class);
        return [
            'products' => $products,
            'categories' => $categories,
            'categoryUrl' => $categoryUrl,
        ];
    }
}
