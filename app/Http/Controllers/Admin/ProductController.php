<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\RequestDTO\Product\Admin\ProductsQuery;
use App\Http\Requests\Product\SaveRequest;
use App\Http\Resources\Categories\CategoryCrudResource;
use App\Http\Resources\General\GeneralPagination;
use App\Http\Resources\Images\ImageCrudResource;
use App\Http\Resources\Products\ProductCrudResource;
use App\Models\Category;
use App\Models\Product;
use App\Services\Product\ProductAdminService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    public function __construct(private readonly ProductAdminService $productAdminService)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        Gate::authorize('viewAny', Product::class);
        $query = ProductsQuery::validateAndCreate($request->query())->toArray();
        $products = function () use ($query) {
            $productsPaginator = $this->productAdminService->getProductsWithPaginate($query);
            if (isset($query['page']) && $query['page'] > $productsPaginator->lastPage()) {
                $query['page'] = $productsPaginator->lastPage();

                return redirect()->route('admin.products.index', $query);
            }
            return GeneralPagination::fromPaginator($productsPaginator, ProductCrudResource::class);
        };
        return Inertia::render('Admin/Products/Index', [
            'products' => $products,
            'categories' => fn() => CategoryCrudResource::collect(Category::type()->orderBy('title')->get()),
            'query' => $query,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        Gate::authorize('create', Product::class);

        return Inertia::render('Admin/Products/Create', [
            'categories' => CategoryCrudResource::collect(Category::type()->orderBy('title')->get()),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SaveRequest $request): RedirectResponse
    {
        Gate::authorize('create', Product::class);
        $data = $request->getData()->toArray();
        $product = Product::create($data);

        return redirect()->route('admin.products.edit', $product);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product): Response
    {
        Gate::authorize('update', $product);
        $product->load('category', 'images');

        return Inertia::render('Admin/Products/Edit', [
            'product' => fn() => ProductCrudResource::from($product),
            'categories' => fn() => CategoryCrudResource::collect(Category::type()->orderBy('title')->get()),
            'images' => fn() => ImageCrudResource::collect($product->images),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SaveRequest $request, Product $product): RedirectResponse
    {
        Gate::authorize('update', $product);
        $data = $request->getData()->toArray();
        $product->update($data);
        return redirect()->back()->with('notice', 'products.updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product): RedirectResponse
    {
        Gate::authorize('delete', $product);
        $this->productAdminService->delete($product);

        return redirect()->route('admin.products.index')->with('notice', 'products.deleted');
    }
}
