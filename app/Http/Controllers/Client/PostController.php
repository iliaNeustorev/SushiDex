<?php

namespace App\Http\Controllers\Client;

use App\Enums\Categories\Type;
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
use App\Services\Post\PostClientService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class PostController extends Controller
{
    public function __construct(
        private readonly PostClientService $postClientService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        Gate::authorize('viewAny', Post::class);
        $user = $request->user();
        $filters = PostsQuery::validateAndCreate($request->query())->toArray();

        $postsPaginator = $this->postClientService->getUserPostsWithPaginate($user, $filters);

        if (isset($filters['page']) && $filters['page'] > $postsPaginator->lastPage()) {
            $filters['page'] = $postsPaginator->lastPage();

            return redirect()->route('my-posts.index', $filters);
        }

        $posts = GeneralPagination::fromPaginator($postsPaginator, PostCrudResource::class);

        $tags = $this->postClientService->getTagsForFilter($filters);
        $statuses = collect(Status::TEXTS);

        return Inertia::render('Posts/Author/Index', [
            'posts' => $posts,
            'categories' => fn () => CategoryCrudResource::collect(Category::byType(Type::BLOG)->get()),
            'statuses' => $statuses,
            'query' => $filters,
            'tags' => fn () => $tags,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        Gate::authorize('create', Post::class);
        $categories = CategoryCrudResource::collect(Category::byType(Type::BLOG)->get());
        $tags = TagCrudResource::collect(Tag::get());

        return Inertia::render('Posts/Author/Create', compact('categories', 'tags'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SaveRequest $request)
    {
        Gate::authorize('create', Post::class);
        $user = $request->user();
        $data = $request->getData()->toArray();

        $newPost = $this->postClientService->createPost($data, $user);

        Inertia::flash(config('app-notifications.posts.store'));

        return redirect()->route('my-posts.edit', $newPost->id);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post)
    {
        if ($post->status === Status::PUBLISHED) {
            return redirect()->back();
        }
        Gate::authorize('update', $post);
        $post->load('category:id,title', 'tags');

        return Inertia::render('Posts/Author/Edit', [
            'categories' => fn () => CategoryCrudResource::collect(Category::byType(Type::BLOG)->get()),
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

        $this->postClientService->updatePost($post, $data);

        return Inertia::flash(config('app-notifications.posts.update'))->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        Gate::authorize('delete', $post);

        $this->postClientService->deletePost($post);

        Inertia::flash(config('app-notifications.posts.destroy'));

        return redirect()->route('my-posts.index');
    }

    public function onModerating(Post $post)
    {
        Gate::authorize('update', $post);

        $this->postClientService->onModerating($post);

        return Inertia::flash(config('app-notifications.posts.moderating'))->back();
    }
}
