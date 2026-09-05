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
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class CategoryController extends Controller
{
    public function __construct(
        protected CategoryAdminService $categoryAdminService
    ) {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filters = CategoriesQuery::validateAndCreate($request->query())->toArray();
        $categories = function () use ($filters) {
            $categoriesPaginator = QueryBuilder::for(Category::class)
                ->allowedFilters([
                    'title',
                    'url',
                    AllowedFilter::exact('type'),
                    AllowedFilter::callback('date_from', fn($q, $v) => $q->where('created_at', '>=', $v)),
                    AllowedFilter::callback('date_to', fn($q, $v) => $q->where('created_at', '<=', $v . ' 23:59:59')),
                ])
                ->allowedSorts(['id', 'title', 'created_at', 'type'])
                ->paginate($filters['batch'] ?? 10);

            return GeneralPagination::fromPaginator($categoriesPaginator, CategoryCrudResource::class);
        };
        return Inertia::render('Admin/Categories/Index', [
            'categories' => $categories,
            'query' => $filters,
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
            'category' => fn() => CategoryCrudResource::from($category),
            'images' => fn() => ImageCrudResource::collect($category->images),
            'categories' => fn() => CategoryCrudResource::collect(Category::whereNot('id', $category->id)->type($category->type)->get()),
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
        if (!$deleted) {
            abort(422, 'К категории привязаны посты или продукты');
        }

        return redirect()->route('admin.categories.index')->with('notice', 'categories.deleted');
    }
}
