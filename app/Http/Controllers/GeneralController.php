<?php

namespace App\Http\Controllers;

use App\Http\RequestDTO\Product\Client\ProductsClientQuery;
use App\Http\Resources\Categories\CategoryPublicResource;
use App\Http\Resources\Products\ProductPublicResource;
use App\Models\Product;
use App\Services\Product\ProductClientService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class GeneralController extends Controller
{

    public function __construct(private readonly ProductClientService $productClientService)
    {
    }

    public function index()
    {
        return Inertia::render('Home', [
            'topProducts' => fn() => ProductPublicResource::collect(Product::with('images')->topSale()->get())
        ]);
    }

    public function menu(Request $request)
    {
        $filters = ProductsClientQuery::validateAndCreate($request->query())->toArray();
        $data = $this->productClientService->getProductWithPagination($filters);
        if (isset($filters['page']) && $filters['page'] > $data['products']->lastPage) {
            $filters['page'] = $data['products']->lastPage;
            return redirect()->route('menu', $filters);
        }
        return Inertia::render('Menu/Index', [
            'categories' => CategoryPublicResource::collect($data['categories']),
            'selectedCategoryUrl' => $data['categoryUrl'],
            'products' => $data['products'],
            'query' => $filters
        ]);
    }
}
