<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Post;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class TrashControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Gate::define('dev', fn (User $user): bool => true);
    }

    #[DataProvider('trashResourceProvider')]
    public function test_developer_can_view_trashed_models(string $resource): void
    {
        $user = User::factory()->create();
        $model = $this->createResource($resource, $user);
        $model->delete();

        $this->actingAs($user)
            ->get(route("admin.{$resource}-trash.index"))
            ->assertOk();
    }

    #[DataProvider('trashResourceProvider')]
    public function test_developer_can_restore_trashed_model(string $resource): void
    {
        $user = User::factory()->create();
        $model = $this->createResource($resource, $user);
        $model->delete();

        $this->assertSoftDeleted($model);

        $this->actingAs($user)
            ->put(route("admin.{$resource}-trash.update", $model->getKey()))
            ->assertRedirect();

        $this->assertNotSoftDeleted($model);
    }

    #[DataProvider('trashResourceProvider')]
    public function test_developer_can_permanently_delete_trashed_model(string $resource): void
    {
        $user = User::factory()->create();
        $model = $this->createResource($resource, $user);
        $model->delete();

        $this->actingAs($user)
            ->delete(route("admin.{$resource}-trash.destroy", $model->getKey()))
            ->assertRedirect();

        $this->assertDatabaseMissing($model->getTable(), [
            $model->getKeyName() => $model->getKey(),
        ]);
    }

    /**
     * @return array<string, array{resource: string}>
     */
    public static function trashResourceProvider(): array
    {
        return [
            'post' => ['post'],
            'category' => ['category'],
            'product' => ['product'],
        ];
    }

    private function createResource(string $resource, User $user): Model
    {
        return match ($resource) {
            'post' => Post::create([
                'url' => 'post-'.Str::uuid(),
                'title' => 'Тестовый пост',
                'content' => 'Содержимое тестового поста',
                'user_id' => $user->id,
                'category_id' => Category::create([
                    'url' => 'post-category-'.Str::uuid(),
                    'title' => 'Категория поста',
                ])->id,
            ]),
            'category' => Category::create([
                'url' => 'category-'.Str::uuid(),
                'title' => 'Тестовая категория',
            ]),
            'product' => Product::create([
                'title' => 'Тестовый продукт',
                'price' => '100.00',
                'category_id' => Category::create([
                    'url' => 'product-category-'.Str::uuid(),
                    'title' => 'Категория продукта',
                ])->id,
            ]),
        };
    }
}
