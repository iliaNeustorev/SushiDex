<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class AdminAccessControlTest extends TestCase
{
    use RefreshDatabase;

    public function test_author_can_open_post_management_pages(): void
    {
        Gate::define('author', fn (User $user): bool => true);
        $author = User::factory()->create();

        $this->actingAs($author)
            ->get(route('admin.posts.index'))
            ->assertOk();

        $this->actingAs($author)
            ->get(route('admin.posts.create'))
            ->assertOk();
    }

    public function test_developer_can_open_category_management_pages(): void
    {
        Gate::define('dev', fn (User $user): bool => true);
        Gate::define('moderator', fn (User $user): bool => true);
        $developer = User::factory()->create();

        $this->actingAs($developer)
            ->get(route('admin.categories.index'))
            ->assertOk();

        $this->actingAs($developer)
            ->get(route('admin.categories.create'))
            ->assertOk();
    }

    public function test_blocked_user_cannot_access_authenticated_pages(): void
    {
        $user = User::factory()->create(['block' => true]);

        $this->actingAs($user)
            ->get(route('profile.index'))
            ->assertForbidden();
    }
}
