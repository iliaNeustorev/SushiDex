<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Inertia\Testing\AssertableInertia as Assert;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ErrorPageTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config(['app.debug' => false]);
        $this->withoutVite();

        Route::middleware('web')->get('/_test/errors/{status}', function (int $status) {
            abort($status);
        });
    }

    public static function statuses(): array
    {
        return [[403], [404], [500]];
    }

    #[DataProvider('statuses')]
    public function test_browser_receives_error_page_with_original_status(int $status): void
    {
        $this->get("/_test/errors/{$status}")
            ->assertStatus($status)
            ->assertInertia(fn (Assert $page) => $page
                ->component('Error')
                ->where('status', $status)
                ->where('user', null));
    }

    #[DataProvider('statuses')]
    public function test_inertia_navigation_receives_error_component(int $status): void
    {
        $version = $this->get("/_test/errors/{$status}")->viewData('page')['version'];

        $this->get("/_test/errors/{$status}", [
            'X-Inertia' => 'true',
            'X-Inertia-Version' => $version,
        ])
            ->assertStatus($status)
            ->assertHeader('X-Inertia', 'true')
            ->assertJsonPath('component', 'Error')
            ->assertJsonPath('props.status', $status);
    }

    #[DataProvider('statuses')]
    public function test_json_requests_keep_json_errors(int $status): void
    {
        $this->getJson("/_test/errors/{$status}")
            ->assertStatus($status)
            ->assertJsonStructure(['message'])
            ->assertJsonMissingPath('component');
    }

    public function test_unknown_url_uses_404_page(): void
    {
        $this->get('/this-page-does-not-exist')
            ->assertNotFound()
            ->assertInertia(fn (Assert $page) => $page->component('Error')->where('status', 404));
    }

    public function test_debug_mode_keeps_server_error_details(): void
    {
        config(['app.debug' => true]);

        $this->get('/_test/errors/500')
            ->assertStatus(500)
            ->assertDontSee('data-page=', false);
    }
}
