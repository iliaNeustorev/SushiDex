<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Posts\Status;
use App\Http\Controllers\Controller;
use App\Http\RequestDTO\Posts\Admin\PostsQuery;
use App\Http\Requests\Post\SaveRequest;
use App\Http\Resources\Categories\CategoryCrudResource;
use App\Http\Resources\General\GeneralPagination;
use App\Http\Resources\Images\ImageCrudResource;
use App\Http\Resources\Posts\PostCrudResource;
use App\Http\Resources\Tags\TagCrudResource;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Services\Image\Service as ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class PostController extends Controller
{
    protected ImageService $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filters = PostsQuery::validateAndCreate($request->query())->toArray();
        $user = $request->user();
        $posts = function () use ($user) {
            $postsPaginator = QueryBuilder::for(Post::class::byUserId($user->id))
                ->with(['user', 'category', 'tags'])
                ->allowedFilters([
                    'title',
                    AllowedFilter::exact('status'),
                    AllowedFilter::callback(
                        'tags',
                        fn ($query, $values) => $query->whereHas('tags', fn ($q) => $q->whereIn('tag_id', (array) $values))
                    ),
                    AllowedFilter::callback('date_from', fn ($q, $v) => $q->where('created_at', '>=', $v)),
                    AllowedFilter::callback('date_to', fn ($q, $v) => $q->where('created_at', '<=', $v.' 23:59:59')),
                ])
                ->orderByDesc('id')
                ->allowedSorts(['id', 'title', 'created_at'])
                ->paginate($query['batch'] ?? 10);

            if (isset($query['page']) && $query['page'] > $postsPaginator->lastPage()) {
                $query['page'] = $postsPaginator->lastPage();

                return redirect()->route('office.posts.index', $query);
            }

            return GeneralPagination::fromPaginator($postsPaginator, PostCrudResource::class);
        };
        $tags = function () use ($filters) {
            $tagsBuilder = Tag::orderBy('url', 'ASC')->limit(5);

            if (isset($filters['tagSearch'])) {
                $tagsBuilder->where('url', 'LIKE', '%'.$filters['tagSearch'].'%');
            }

            $tagsBySearch = $tagsBuilder->get();

            if (isset($filters['filter']['tags'])) {
                $tagsBySearch = $tagsBySearch->merge(
                    Tag::whereIn('id', explode(',', $filters['filter']['tags']))->get()
                );
            }

            return TagCrudResource::collect($tagsBySearch);
        };
        $statuses = collect(Status::TEXTS);
        $categories = CategoryCrudResource::collect(Category::get());

        return Inertia::render('Office/Posts/Index', [
            'posts' => $posts,
            'categories' => $categories,
            'statuses' => $statuses,
            'query' => $filters,
            'tags' => $tags,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = CategoryCrudResource::collect(Category::get());
        $tags = TagCrudResource::collect(Tag::get());

        return Inertia::render('Office/Posts/Create', compact('categories', 'tags'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SaveRequest $request)
    {
        Gate::authorize('create', Post::class);
        $user = $request->user();
        $data = $request->getData()->toArray() + ['user_id' => $user->id, 'status' => Status::DRAFT];
        $newPost = Post::create($data);
        if (! empty($data['tags'])) {
            $newPost->tags()->attach($data['tags']);
        }

        return redirect()->route('admin.posts.edit', $newPost->id);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post)
    {
        $post->load('category:id,title', 'user', 'tags');

        return Inertia::render('Office/Posts/Edit', [
            'categories' => fn () => CategoryCrudResource::collect(Category::get()),
            'post' => fn () => $post,
            'tags' => fn () => TagCrudResource::collect(Tag::get()),
            'images' => fn () => ImageCrudResource::collect($post->images),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SaveRequest $request, Post $post)
    {
        Gate::authorize('update', $post);
        $data = $request->getData()->toArray();
        $post->update($data);
        if (! empty($data['tags'])) {
            $post->tags()->sync($data['tags']);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        Gate::authorize('delete', $post);
        $post->delete();
        $this->imageService->deleteAllImagesModel($post, ['catalogPath' => 'posts']);

        return redirect()->route('admin.posts.index')->with('notice', 'posts.deleted');
    }

    /**
     * @return RedirectResponse
     */
    public function publish(Post $post)
    {
        if ($post->status !== Status::DRAFT) {
            abort(400);
        }
        $post->status = Status::MODERATING;
        $post->save();

        return redirect()->route('admin.posts.index')->with('notice', 'posts.updated');
    }
}
