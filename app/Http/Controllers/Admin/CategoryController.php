<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Category\SaveRequest;
use App\Http\Resources\Categories\CategoryCrudResource;
use App\Http\Resources\Images\ImageCrudResource;
use App\Models\Category;
use App\Services\Category\CategoryAdminService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Spatie\LaravelData\Exceptions\InvalidDataClass;

class CategoryController extends Controller
{
    public function __construct(
        protected CategoryAdminService $categoryAdminService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::orderBy('title')->get();

        return view('categories/index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Admin/Categories/Create', []);
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
            abort(422, 'К категории привязаны посты или продукты');
        }

        return redirect()->route('admin.categories.index')->with('notice', 'categories.deleted');
    }
}
