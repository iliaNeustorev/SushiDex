<?php

namespace Tests\Feature;

use App\Enums\Posts\Status;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class PostStatusTransitionTest extends TestCase
{
    use RefreshDatabase;

    public function test_author_can_send_their_draft_to_moderation(): void
    {
        Gate::define('moderator', fn (User $user): bool => false);
        $author = User::factory()->create();
        $post = $this->createPost($author, Status::DRAFT);

        $this->actingAs($author)
            ->put(route('posts.moderating', $post))
            ->assertRedirect(route('admin.posts.index'))
            ->assertInertiaFlash('type', 'info')
            ->assertInertiaFlash('text', 'Пост отправлен на модерацию');

        $this->assertSame(Status::MODERATING, $post->fresh()->status);
    }

    public function test_user_cannot_send_another_users_draft_to_moderation(): void
    {
        Gate::define('moderator', fn (User $user): bool => false);
        $author = User::factory()->create();
        $anotherUser = User::factory()->create();
        $post = $this->createPost($author, Status::DRAFT);

        $this->actingAs($anotherUser)
            ->put(route('posts.moderating', $post))
            ->assertForbidden();

        $this->assertSame(Status::DRAFT, $post->fresh()->status);
    }

    public function test_moderator_can_publish_a_post_under_moderation(): void
    {
        $moderator = User::factory()->create();
        Gate::define('moderator', fn (User $user): bool => $user->is($moderator));
        $post = $this->createPost(User::factory()->create(), Status::MODERATING);

        $this->actingAs($moderator)
            ->patch(route('admin.posts.publish', $post))
            ->assertRedirect(route('admin.posts.index'))
            ->assertInertiaFlash('type', 'success')
            ->assertInertiaFlash('text', 'Пост опубликован');

        $this->assertSame(Status::PUBLISHED, $post->fresh()->status);
    }

    public function test_moderator_cannot_publish_a_draft(): void
    {
        $moderator = User::factory()->create();
        Gate::define('moderator', fn (User $user): bool => $user->is($moderator));
        $post = $this->createPost(User::factory()->create(), Status::DRAFT);

        $this->actingAs($moderator)
            ->patch(route('admin.posts.publish', $post))
            ->assertBadRequest();

        $this->assertSame(Status::DRAFT, $post->fresh()->status);
    }

    private function createPost(User $author, Status $status): Post
    {
        return Post::create([
            'url' => fake()->unique()->slug(),
            'title' => fake()->sentence(),
            'content' => fake()->paragraph(),
            'status' => $status,
            'user_id' => $author->id,
        ]);
    }
}
