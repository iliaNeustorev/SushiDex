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
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class ProductController extends Controller
{
    public function __construct(private readonly ProductAdminService $products)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        Gate::authorize('viewAny', Product::class);
        $query = ProductsQuery::validateAndCreate($request->query())->toArray();

        return Inertia::render('Admin/Products/Index', [
            'products' => function () use ($query) {
                $paginator = QueryBuilder::for(Product::class)
                    ->with('category')
                    ->allowedFilters([
                        'title',
                        AllowedFilter::exact('category_id'),
                        AllowedFilter::callback('date_from', fn($builder, $value) => $builder->where('created_at', '>=', $value)),
                        AllowedFilter::callback('date_to', fn($builder, $value) => $builder->where('created_at', '<=', $value . ' 23:59:59')),
                    ])
                    ->defaultSort('-id')
                    ->allowedSorts(['id', 'title', 'price', 'created_at'])
                    ->paginate($query['batch'] ?? 10);

                return GeneralPagination::fromPaginator($paginator, ProductCrudResource::class);
            },
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
            'categories' => fn() => CategoryCrudResource::collect(Category::type()->orderBy('title')->get()),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SaveRequest $request): RedirectResponse
    {
        Gate::authorize('create', Product::class);
        $product = $this->products->create($request->getData());

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
        $this->products->update($product, $request->getData());

        return redirect()->back()->with('notice', 'products.updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product): RedirectResponse
    {
        Gate::authorize('delete', $product);
        $this->products->delete($product);

        return redirect()->route('admin.products.index')->with('notice', 'products.deleted');
    }
}
