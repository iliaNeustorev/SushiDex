<?php

namespace App\Http\Controllers\Admin\Trash;

use App\Http\Controllers\Controller;
use App\Http\RequestDTO\Product\Admin\ProductsTrashQuery;
use App\Http\Resources\General\GeneralPagination;
use App\Http\Resources\Products\ProductTrashResource;
use App\Models\Product;
use App\Services\Product\ProductAdminService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class ProductTrashController extends Controller
{
    public function __construct(
        private readonly ProductAdminService $productAdminService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = ProductsTrashQuery::validateAndCreate($request->query())->toArray();
        $productsPaginator = $this->productAdminService->getTrashedProductsWithPaginate($query);
        if (isset($query['page']) && $query['page'] > $productsPaginator->lastPage()) {
            $query['page'] = $productsPaginator->lastPage();

            return redirect()->route('admin.product-trash.index', $query);
        }
        if ($productsPaginator->total() === 0) {
            return redirect()->route('admin.products.index');
        }
        $products = GeneralPagination::from($productsPaginator, ProductTrashResource::class);

        return Inertia::render('Admin/Products/Trash/Index', [
            'products' => $products,
            'query' => $query,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(int $productTrashId)
    {
        $productTrash = Product::onlyTrashed()
            ->where('id', $productTrashId)
            ->firstOrFail();
        Gate::authorize('restore', $productTrash);
        $productTrash->restore();

        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $productTrashId)
    {
        $productTrash = Product::onlyTrashed()
            ->where('id', $productTrashId)
            ->firstOrFail();
        Gate::authorize('forceDelete', $productTrash);
        $productTrash->forceDelete();

        return redirect()->back();
    }
}
