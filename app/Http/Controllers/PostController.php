<?php

namespace App\Http\Controllers;

use App\Enums\Posts\Status;
use App\Http\Resources\Posts\PostPublicResource;
use App\Models\Post;
use App\Services\Post\PostClientService;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class PostController extends Controller
{
    public function __construct(
        private readonly PostClientService $postClientService
    ) {
    }

    public function index()
    {
        $paginator = $this->postClientService->getPublishedPostsWithPaginate();
        $posts = $paginator->items();
        $page = $paginator->currentPage();
        $lastPage = $paginator->lastPage();
        if ($page > $lastPage) {
            return redirect()->route('posts.index', ['page' => 1]);
        }

        return Inertia::render('Posts/Index', [
            'posts' => Inertia::merge($posts),
            'lastPage' => $lastPage,
            'page' => $page,
        ]);
    }

    public function show(Post $post)
    {
        if ($post->status !== Status::PUBLISHED) {
            return redirect()->route('posts.index', ['page' => 1]);
        }
        $post->loadMissing('user', 'category', 'tags');

        return Inertia::render('Posts/Show', [
            'post' => PostPublicResource::from($post),
        ]);
    }

    public function moderating(Post $post)
    {
        Gate::authorize('update', $post);

        if ($post->status !== Status::DRAFT) {
            abort(400);
        }
        $post->status = Status::MODERATING;
        $post->save();

        Inertia::flash(config('app-notifications.posts.moderating'));

        return redirect()->route('admin.posts.index');
    }
}
