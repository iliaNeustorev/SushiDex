<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\RequestDTO\Tags\Admin\TagsQuery;
use App\Http\Requests\Tag\SaveRequest;
use App\Http\Resources\General\GeneralPagination;
use App\Http\Resources\Tags\TagCrudResource;
use App\Models\Tag;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\QueryBuilder\QueryBuilder;

class TagController extends Controller
{
    public function index(Request $request)
    {
        $filters = TagsQuery::validateAndCreate($request->query())->toArray();
        $tags = function () use ($filters) {
            $tagsPaginator = QueryBuilder::for(Tag::class)
                ->allowedFilters([
                    'url',
                    'title',
                ])
                ->allowedSorts(['id', 'title', 'url', 'created_at'])
                ->paginate($filters['batch'] ?? 10);

            return GeneralPagination::fromPaginator($tagsPaginator, TagCrudResource::class);
        };

        return Inertia::render('Admin/Tags/Index', [
            'tags' => $tags,
            'query' => $filters,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Admin/Tags/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SaveRequest $request)
    {
        $data = $request->getData()->toArray();
        Tag::create($data);

        return redirect()->route('admin.tags.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tag $tag)
    {
        return Inertia::render('Admin/Tags/Edit', [
            'tag' => $tag
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SaveRequest $request, Tag $tag)
    {
        $data = $request->getData()->toArray();
        $tag->update($data);

        return redirect()->route('admin.tags.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tag $tag)
    {
        $tag->delete();
        $tag->posts()->detach();

        return redirect()->route('admin.tags.index');
    }
}
