<?php

namespace App\Services\Post;

use App\Models\Post;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\QueryBuilder\QueryBuilder;

class PostClientService
{
    public function getPublishedPostsWithPaginate(): LengthAwarePaginator
    {
        return QueryBuilder::for(Post::class::isPublished())
            ->with(['user', 'category', 'tags'])
            ->allowedFilters(['title'])
            ->paginate(5);
    }
}
