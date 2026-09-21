<?php

namespace App\Http\Controllers;

use App\Http\Resources\Posts\PostPublicResource;
use App\Models\Post;
use App\Services\Post\PostClientService;
use Inertia\Inertia;

class PostController extends Controller
{

    public function __construct(private readonly PostClientService $postClientService)
    {
    }

    public function index()
    {
        $paginator = $this->postClientService->getPostsWithPaginate();
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
        $post->loadMissing('user', 'category', 'tags');

        return Inertia::render('Posts/Show', [
            'post' => PostPublicResource::from($post),
        ]);
    }
}
