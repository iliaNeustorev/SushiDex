<?php

namespace Tests\Feature;

use App\Jobs\ResizeImage;
use App\Models\Image;
use App\Models\User;
use App\Services\Image\Service as ImageService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Tests\TestCase;

class SingleImageServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_replacing_avatar_keeps_one_image_and_removes_old_file(): void
    {
        Storage::fake('public');
        Queue::fake();

        $user = User::factory()->create();
        $oldPath = "images/avatars/{$user->id}/old.jpg";
        Storage::disk('public')->put($oldPath, 'old image');
        $image = $user->image()->create(['path' => $oldPath, 'name' => 'old']);

        $saved = app(ImageService::class)->saveOneImage($user, [
            'image' => UploadedFile::fake()->image('avatar.jpg'),
            'catalogPath' => 'avatars',
        ]);

        $this->assertTrue($saved);
        $this->assertSame(1, Image::where('imageable_type', User::class)->where('imageable_id', $user->id)->count());
        $newImage = $user->image()->firstOrFail();
        $this->assertNotSame($image->id, $newImage->id);
        $this->assertNotSame($oldPath, $newImage->path);
        Storage::disk('public')->assertMissing($oldPath);
        Storage::disk('public')->assertExists($newImage->path);
        Queue::assertPushed(ResizeImage::class);
    }

    public function test_failed_avatar_creation_leaves_no_image_or_file(): void
    {
        Storage::fake('public');
        Queue::fake();

        $user = User::factory()->create();
        $oldPath = "images/avatars/{$user->id}/old.jpg";
        Storage::disk('public')->put($oldPath, 'old image');
        $user->image()->create(['path' => $oldPath, 'name' => 'old']);
        Image::creating(fn (): never => throw new RuntimeException('Database insert failed'));

        $saved = app(ImageService::class)->saveOneImage($user, [
            'image' => UploadedFile::fake()->image('avatar.jpg'),
            'catalogPath' => 'avatars',
        ]);

        $this->assertFalse($saved);
        $this->assertNull($user->image()->first());
        $this->assertSame([], Storage::disk('public')->allFiles("images/avatars/{$user->id}"));
        Queue::assertNothingPushed();
    }
}
