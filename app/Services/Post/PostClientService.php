<?php

namespace App\Services\Post;

use App\Enums\Posts\Status;
use App\Helpers\Spattie\CustomSort\SortByFieldRelation;
use App\Http\Resources\Tags\TagCrudResource;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use App\Services\Image\Service as ImageService;
use Closure;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class PostClientService
{
    public function __construct(
        private ImageService $imageService
    ) {}

    public function getPublishedPostsWithPaginate(): LengthAwarePaginator
    {
        return QueryBuilder::for(Post::class::isPublished())
            ->with(['user', 'category', 'tags'])
            ->allowedFilters(['title'])
            ->paginate(5);
    }

    public function getUserPostsWithPaginate(User $user, array $query): LengthAwarePaginator
    {
        return QueryBuilder::for(Post::byUserId($user->id))
            ->with(['category', 'tags'])
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
            ->defaultSort('-id')
            ->allowedSorts([
                'id',
                'title',
                'created_at',
                AllowedSort::custom('category', new SortByFieldRelation('category'), 'title'),
            ])
            ->paginate($query['batch'] ?? 10);
    }

    public function getTagsForFilter(array $filters): Closure
    {
        return function () use ($filters) {
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
    }

    public function createPost(array $data, User $user): Post
    {
        $data = $data + ['user_id' => $user->id, 'status' => Status::DRAFT];
        $post = Post::create($data);
        if (! empty($data['tags'])) {
            $post->tags()->attach($data['tags']);
        }

        return $post;
    }

    public function updatePost(Post $post, array $data): void
    {
        $post->update($data);
        $post->tags()->sync($data['tags'] ?? []);
    }

    public function deletePost(Post $post): void
    {
        $post->delete();
        $this->imageService->deleteAllImagesModel($post, ['catalogPath' => 'posts']);
    }

    public function onModerating(Post $post): void
    {
        if ($post->status === Status::MODERATING || $post->status === Status::PUBLISHED) {
            abort(400);
        }
        $post->status = Status::MODERATING;
        $post->save();
    }
}
