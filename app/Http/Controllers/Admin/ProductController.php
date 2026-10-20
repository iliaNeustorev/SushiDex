<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Categories\Type;
use App\Http\Controllers\Controller;
use App\Http\RequestDTO\Product\Admin\ProductsQuery;
use App\Http\Requests\Product\ChangeActiveRequest;
use App\Http\Requests\Product\SaveRequest;
use App\Http\Resources\Categories\CategoryCrudResource;
use App\Http\Resources\General\GeneralPagination;
use App\Http\Resources\Images\ImageCrudResource;
use App\Http\Resources\Products\ProductCrudResource;
use App\Models\Category;
use App\Models\Product;
use App\Services\Product\ProductAdminService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class ProductController extends Controller
{
    public function __construct(
        private readonly ProductAdminService $productAdminService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        Gate::authorize('viewAny', Product::class);
        $query = ProductsQuery::validateAndCreate($request->query())->toArray();

        $productsPaginator = $this->productAdminService->getProductsWithPaginate($query);

        if (isset($query['page']) && $query['page'] > $productsPaginator->lastPage()) {
            $query['page'] = $productsPaginator->lastPage();

            return redirect()->route('admin.products.index', $query);
        }

        $products = GeneralPagination::fromPaginator($productsPaginator, ProductCrudResource::class);

        return Inertia::render('Admin/Products/Index', [
            'products' => $products,
            'categories' => fn () => CategoryCrudResource::collect(Category::byType(Type::PRODUCT)->orderBy('title')->get()),
            'query' => $query,
            'countDeletedProduct' => fn () => Product::onlyTrashed()->count(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        Gate::authorize('create', Product::class);

        return Inertia::render('Admin/Products/Create', [
            'categories' => CategoryCrudResource::collect(Category::byType(Type::PRODUCT)->orderBy('title')->get()),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SaveRequest $request)
    {
        Gate::authorize('create', Product::class);
        $data = $request->getData()->toArray();
        $product = Product::create($data);

        Inertia::flash(config('app-notifications.products.admin.store'));

        return redirect()->route('admin.products.edit', $product);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        Gate::authorize('update', $product);
        $product->load('category', 'images');

        return Inertia::render('Admin/Products/Edit', [
            'product' => fn () => ProductCrudResource::from($product),
            'categories' => fn () => CategoryCrudResource::collect(Category::byType(Type::PRODUCT)->orderBy('title')->get()),
            'images' => fn () => ImageCrudResource::collect($product->images),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SaveRequest $request, Product $product)
    {
        Gate::authorize('update', $product);
        $data = $request->getData()->toArray();
        $product->update($data);

        return Inertia::flash(config('app-notifications.products.admin.update'))->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        Gate::authorize('delete', $product);
        try {
            $this->productAdminService->delete($product);
            Inertia::flash(config('app-notifications.products.admin.destroy'));
        } catch (\Exception $e) {
            Log::error('Не удалось удалить продукт', ['id' => $product->id]);
            report($e);
            Inertia::flash(config('app-notifications.products.admin.errors.destroy'));
        }

        return redirect()->route('admin.products.index');
    }

    public function changeActive(ChangeActiveRequest $request, Product $product)
    {
        Gate::authorize('update', $product);
        $this->productAdminService->changeActive($product, $request->getData()->active);

        return Inertia::flash(config('app-notifications.products.admin.active'))->back();
    }
}
