<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class AdminPaginationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Gate::define('author', fn (User $user): bool => true);
        Gate::define('dev', fn (User $user): bool => true);
    }

    public function test_posts_page_redirects_to_the_last_available_page(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('admin.posts.index', ['page' => 2]))
            ->assertRedirect(route('admin.posts.index', ['page' => 1]));
    }

    public function test_products_page_redirects_to_the_last_available_page(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('admin.products.index', ['page' => 2]))
            ->assertRedirect(route('admin.products.index', ['page' => 1]));
    }
}
