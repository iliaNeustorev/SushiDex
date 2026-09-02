<?php

namespace App\Http\Controllers;

use App\Enums\Posts\Status;
use App\Http\Resources\Posts\PostPublicResource;
use App\Models\Post;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\QueryBuilder\QueryBuilder;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $paginator = QueryBuilder::for(Post::class::byUserId($user?->id)->where('status', Status::PUBLISHED))
            ->with(['user', 'category', 'tags'])
            ->allowedFilters(['title'])
            ->paginate(5);

        $posts = $paginator->items();
        $page = $paginator->currentPage();
        $lastPage = $paginator->lastPage();

        return Inertia::render('Posts/Index', [
            'posts' => Inertia::merge($posts),
            'lastPage' => $lastPage,
            'page' => $page,
        ]);
    }

    public function show(Post $post)
    {
        $post->loadMissing('user', 'category', 'tags');

        return Inertia::render('Posts/Show', [
            'post' => PostPublicResource::from($post),
        ]);
    }
}
