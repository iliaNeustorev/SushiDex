<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\SaveRequest;
use App\Http\Resources\Categories\CategoryCrudResource;
use App\Http\Resources\Images\ImageCrudResource;
use App\Http\Resources\Products\ProductCrudResource;
use App\Models\Category;
use App\Models\Product;
use App\Services\Product\ProductAdminService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    public function __construct(private readonly ProductAdminService $products) {}

    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        Gate::authorize('viewAny', Product::class);

        return Inertia::render('Office/Products/Index', [
            'products' => fn () => ProductCrudResource::collect(Product::with('category')->latest()->get()),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        Gate::authorize('create', Product::class);

        return Inertia::render('Office/Products/Create', [
            'categories' => fn () => CategoryCrudResource::collect(Category::orderBy('title')->get()),
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

        return Inertia::render('Office/Products/Edit', [
            'product' => fn () => ProductCrudResource::from($product),
            'categories' => fn () => CategoryCrudResource::collect(Category::orderBy('title')->get()),
            'images' => fn () => ImageCrudResource::collect($product->images),
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
