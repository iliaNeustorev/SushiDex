<?php

namespace App\Http\Controllers\Admin\Trash;

use App\Http\Controllers\Controller;
use App\Http\RequestDTO\Posts\Admin\PostsTrashQuery;
use App\Http\Resources\General\GeneralPagination;
use App\Http\Resources\Posts\PostTrashResource;
use App\Models\Post;
use App\Services\Post\PostAdminService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class PostTrashController extends Controller
{
    public function __construct(
        private readonly PostAdminService $postAdminService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = PostsTrashQuery::validateAndCreate($request->query())->toArray();
        $postsPaginator = $this->postAdminService->getTrashedPostsWithPaginate($query);
        if (isset($query['page']) && $query['page'] > $postsPaginator->lastPage()) {
            $query['page'] = $postsPaginator->lastPage();

            return redirect()->route('admin.post-trash.index', $query);
        }
        if ($postsPaginator->total() === 0) {
            return redirect()->route('admin.posts.index');
        }
        $posts = GeneralPagination::from($postsPaginator, PostTrashResource::class);

        return Inertia::render('Admin/Posts/Trash/Index', compact('posts', 'query'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(int $postTrashId)
    {
        $postTrash = Post::onlyTrashed()
            ->where('id', $postTrashId)
            ->firstOrFail();
        Gate::authorize('restore', $postTrash);
        $postTrash->restore();

        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $postTrashId)
    {
        $postTrash = Post::onlyTrashed()
            ->where('id', $postTrashId)
            ->firstOrFail();
        Gate::authorize('forceDelete', $postTrash);
        $postTrash->forceDelete();

        return redirect()->back();
    }
}
