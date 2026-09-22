<?php

namespace App\Http\Controllers\Admin\Trash;

use App\Http\Controllers\Controller;
use App\Http\RequestDTO\Category\Admin\CategoriesTrashQuery;
use App\Http\Resources\Categories\CategoryCrudResource;
use App\Http\Resources\General\GeneralPagination;
use App\Models\Category;
use App\Services\Category\CategoryAdminService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class CategoryTrashController extends Controller
{
    public function __construct(
        private readonly CategoryAdminService $categoryAdminService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = CategoriesTrashQuery::validateAndCreate($request->query())->toArray();
        $categoriesPaginator = $this->categoryAdminService->getTrashedCategoriesWithPaginate($query);
        if (isset($query['page']) && $query['page'] > $categoriesPaginator->lastPage()) {
            $query['page'] = $categoriesPaginator->lastPage();

            return redirect()->route('admin.category-trash.index', $query);
        }
        if ($categoriesPaginator->total() === 0) {
            return redirect()->route('admin.categories.index');
        }
        $categories = GeneralPagination::from($categoriesPaginator, CategoryCrudResource::class);

        return Inertia::render('Admin/Categories/Trash/Index', compact('categories', 'query'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(int $categoryTrashId)
    {
        $categoryTrash = Category::onlyTrashed()
            ->where('id', $categoryTrashId)
            ->firstOrFail();
        Gate::authorize('restore', $categoryTrash);
        $categoryTrash->restore();

        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $categoryTrashId)
    {
        $categoryTrash = Category::onlyTrashed()
            ->where('id', $categoryTrashId)
            ->firstOrFail();
        Gate::authorize('forceDelete', $categoryTrash);
        $categoryTrash->forceDelete();

        return redirect()->back();
    }
}
