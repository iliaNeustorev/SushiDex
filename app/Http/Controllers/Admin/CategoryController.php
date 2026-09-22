<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Categories\Type;
use App\Http\Controllers\Controller;
use App\Http\RequestDTO\Category\Admin\CategoriesQuery;
use App\Http\Requests\Category\SaveRequest;
use App\Http\Resources\Categories\CategoryCrudResource;
use App\Http\Resources\General\GeneralPagination;
use App\Http\Resources\Images\ImageCrudResource;
use App\Models\Category;
use App\Services\Category\CategoryAdminService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\LaravelData\Exceptions\InvalidDataClass;

class CategoryController extends Controller
{
    public function __construct(
        private readonly CategoryAdminService $categoryAdminService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = CategoriesQuery::validateAndCreate($request->query())->toArray();
        $categories = function () use ($query) {
            $categoriesPaginator = $this->categoryAdminService->getCategoriesWithPaginate($query);

            return GeneralPagination::fromPaginator($categoriesPaginator, CategoryCrudResource::class);
        };

        return Inertia::render('Admin/Categories/Index', [
            'categories' => fn () => $categories,
            'query' => $query,
            'countDeletedCategory' => fn () => Category::onlyTrashed()->count(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $types = collect(Type::TEXTS);
        $categories = CategoryCrudResource::collect(Category::get());

        return Inertia::render('Admin/Categories/Create', compact('types', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return RedirectResponse
     *
     * @throws InvalidDataClass
     */
    public function store(SaveRequest $request)
    {
        $data = $request->getData()->toArray();
        $category = Category::create($data);

        return redirect()->route('admin.categories.edit', $category->id);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        return Inertia::render('Admin/Categories/Edit', [
            'category' => fn () => CategoryCrudResource::from($category),
            'images' => fn () => ImageCrudResource::collect($category->images),
            'categories' => fn () => CategoryCrudResource::collect(Category::whereNot('id', $category->id)->byType($category->type)->get()),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SaveRequest $request, Category $category)
    {
        $data = $request->getData()->toArray();
        $category->update($data);

        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @throws Exception
     */
    public function destroy(Category $category)
    {
        $deleted = $this->categoryAdminService->delete($category);
        if (! $deleted) {
            return redirect()->route('admin.categories.index')->withErrors(['error', 'categories.deleted']);
        }

        return redirect()->route('admin.categories.index')->with('notice', 'categories.deleted');
    }
}
