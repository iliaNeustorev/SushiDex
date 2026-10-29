<?php

namespace Tests\Feature;

use App\Enums\Categories\Type;
use App\Enums\Posts\Status;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class AuthorPostControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $author;

    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->author = User::factory()->create();
        $this->category = Category::create(['url' => 'blog', 'title' => 'Блог', 'type' => Type::BLOG]);
        Gate::define('author', fn (User $user): bool => $user->is($this->author));
        Gate::define('moderator', fn (User $user): bool => false);
    }

    public function test_author_sees_only_their_posts_and_filter_tags(): void
    {
        $post = $this->createPost($this->author);
        $this->createPost(User::factory()->create());
        $tag = Tag::create(['url' => 'sushi', 'title' => 'Суши']);

        $this->actingAs($this->author)->get(route('my-posts.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Posts/Author/Index')
                ->has('posts.data', 1)
                ->where('posts.data.0.id', $post->id)
                ->has('tags', 1)
                ->where('tags.0.id', $tag->id));
    }

    public function test_non_author_cannot_access_author_pages(): void
    {
        $this->actingAs(User::factory()->create())->get(route('my-posts.index'))->assertForbidden();
        $this->get(route('my-posts.create'))->assertForbidden();
    }

    public function test_author_can_create_a_draft_with_tags_and_cannot_spoof_owner_or_status(): void
    {
        $tag = Tag::create(['url' => 'sushi', 'title' => 'Суши']);
        $other = User::factory()->create();

        $response = $this->actingAs($this->author)->post(route('my-posts.store'), array_replace($this->payload(), [
            'user_id' => $other->id,
            'status' => Status::PUBLISHED->value,
            'tags' => [$tag->id],
        ]));

        $post = Post::sole();
        $response->assertRedirect(route('my-posts.edit', $post));
        $this->assertSame($this->author->id, $post->user_id);
        $this->assertSame(Status::DRAFT, $post->status);
        $this->assertSame([$tag->id], $post->tags()->pluck('tags.id')->all());

        $this->put(route('my-posts.update', $post), array_replace($this->payload(), ['tags' => [$tag->id]]))
            ->assertRedirect();
        $this->assertSame([$tag->id], $post->tags()->pluck('tags.id')->all());
    }

    public function test_author_can_clear_all_tags(): void
    {
        $post = $this->createPost($this->author);
        $tag = Tag::create(['url' => 'sushi', 'title' => 'Суши']);
        $post->tags()->attach($tag);

        $this->actingAs($this->author)->put(route('my-posts.update', $post), $this->payload())
            ->assertRedirect();

        $this->assertSame(0, $post->tags()->count());
    }

    public function test_author_cannot_edit_or_delete_another_authors_post(): void
    {
        $post = $this->createPost(User::factory()->create());

        $this->actingAs($this->author)->get(route('my-posts.edit', $post))->assertForbidden();
        $this->put(route('my-posts.update', $post), $this->payload())->assertForbidden();
        $this->delete(route('my-posts.destroy', $post))->assertForbidden();
        $this->patch(route('my-posts.moderating', $post))->assertForbidden();
        $this->assertNotSoftDeleted($post);
        $this->assertSame(Status::DRAFT, $post->fresh()->status);
    }

    public function test_author_cannot_change_a_published_post_using_a_direct_request(): void
    {
        $post = $this->createPost($this->author, Status::PUBLISHED);
        $title = $post->title;

        $this->actingAs($this->author)->put(route('my-posts.update', $post), $this->payload())
            ->assertForbidden();

        $this->assertSame($title, $post->fresh()->title);
    }

    public function test_author_can_delete_their_draft(): void
    {
        $post = $this->createPost($this->author);

        $this->actingAs($this->author)->delete(route('my-posts.destroy', $post))
            ->assertRedirect(route('my-posts.index'));

        $this->assertSoftDeleted($post);
    }

    public function test_author_can_send_a_draft_or_rejected_post_to_moderation(): void
    {
        foreach ([Status::DRAFT, Status::REJECTED] as $status) {
            $post = $this->createPost($this->author, $status);

            $this->actingAs($this->author)->from(route('my-posts.index'))
                ->patch(route('my-posts.moderating', $post))
                ->assertRedirect(route('my-posts.index'));

            $this->assertSame(Status::MODERATING, $post->fresh()->status);
        }
    }

    public function test_non_author_cannot_send_a_post_to_moderation(): void
    {
        $user = User::factory()->create();
        $post = $this->createPost($user);

        $this->actingAs($user)->patch(route('my-posts.moderating', $post))->assertForbidden();

        $this->assertSame(Status::DRAFT, $post->fresh()->status);
    }

    public function test_moderation_rejects_published_or_already_moderating_posts(): void
    {
        foreach ([Status::PUBLISHED, Status::MODERATING] as $status) {
            $post = $this->createPost($this->author, $status);
            $this->actingAs($this->author)->patch(route('my-posts.moderating', $post))
                ->assertStatus($status === Status::PUBLISHED ? 403 : 400);
            $this->assertSame($status, $post->fresh()->status);
        }
    }

    public function test_public_cannot_view_unpublished_posts(): void
    {
        foreach ([Status::DRAFT, Status::MODERATING, Status::REJECTED] as $status) {
            $post = $this->createPost($this->author, $status);
            $this->get(route('posts.show', $post))->assertRedirect(route('posts.index', ['page' => 1]));
        }
    }

    private function payload(): array
    {
        return [
            'url' => 'updated-post',
            'title' => 'Обновлённый пост',
            'content' => 'Текст обновлённого поста для проверки.',
            'category_id' => $this->category->id,
            'tags' => [],
        ];
    }

    private function createPost(User $user, Status $status = Status::DRAFT): Post
    {
        return Post::create([
            'url' => fake()->unique()->lexify('post-????????'),
            'title' => 'Исходный пост',
            'content' => 'Исходный текст поста для проверки.',
            'category_id' => $this->category->id,
            'user_id' => $user->id,
            'status' => $status,
        ]);
    }
}
