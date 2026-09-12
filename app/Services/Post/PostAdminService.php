<?php

namespace App\Services\Post;

use App\Models\Post;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class PostAdminService
{
    public function __construct()
    {
        //
    }

    public function getPostsWithPaginate(array $query): LengthAwarePaginator
    {
        return QueryBuilder::for(Post::class)
            ->with(['user', 'category', 'tags'])
            ->allowedFilters([
                'title',
                AllowedFilter::exact('status'),
                AllowedFilter::callback(
                    'tags',
                    fn($query, $values) => $query->whereHas('tags', fn($q) => $q->whereIn('tag_id', (array)$values))
                ),
                AllowedFilter::callback('date_from', fn($q, $v) => $q->where('created_at', '>=', $v)),
                AllowedFilter::callback('date_to', fn($q, $v) => $q->where('created_at', '<=', $v . ' 23:59:59')),
            ])
            ->allowedSorts(['id', 'title', 'created_at'])
            ->paginate($query['batch'] ?? 10);
    }
}
