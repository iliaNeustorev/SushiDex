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
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\LaravelData\Exceptions\InvalidDataClass;

class CategoryController extends Controller
{
    public function __construct(
        private readonly CategoryAdminService $categoryAdminService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        Gate::authorize('viewAny', Category::class);
        $query = CategoriesQuery::validateAndCreate($request->query())->toArray();

        $categoriesPaginator = $this->categoryAdminService->getCategoriesWithPaginate($query);
        $categories = GeneralPagination::fromPaginator($categoriesPaginator, CategoryCrudResource::class);

        return Inertia::render('Admin/Categories/Index', [
            'categories' => fn () => $categories,
            'query' => $query,
            'countDeletedCategory' => fn () => Category::onlyTrashed()->count(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        Gate::authorize('create', Category::class);
        $types = collect(Type::TEXTS);
        $categories = CategoryCrudResource::collect(Category::get());

        return Inertia::render('Admin/Categories/Create', compact('types', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     *
     * @throws InvalidDataClass
     */
    public function store(SaveRequest $request): RedirectResponse
    {
        Gate::authorize('create', Category::class);
        $data = $request->getData()->toArray();
        $category = Category::create($data);

        return redirect()->route('admin.categories.edit', $category->id);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category): Response
    {
        Gate::authorize('update', $category);

        return Inertia::render('Admin/Categories/Edit', [
            'category' => fn () => CategoryCrudResource::from($category),
            'images' => fn () => ImageCrudResource::collect($category->images),
            'categories' => fn () => CategoryCrudResource::collect(Category::whereNot('id', $category->id)->byType($category->type)->get()),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SaveRequest $request, Category $category): RedirectResponse
    {
        Gate::authorize('update', $category);
        $data = $request->getData()->toArray();
        $category->update($data);

        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @throws Exception
     */
    public function destroy(Category $category): RedirectResponse
    {
        Gate::authorize('delete', $category);
        $deleted = $this->categoryAdminService->delete($category);
        if (! $deleted) {
            return redirect()->route('admin.categories.index')->withErrors(['error', 'categories.deleted']);
        }

        return redirect()->route('admin.categories.index')->with('notice', 'categories.deleted');
    }
}
