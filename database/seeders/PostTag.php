<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\Tag;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PostTag extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tags = Tag::get();
        $posts = Post::get();
        $posts->each(fn (Post $post) => $post->tags()->sync($tags->random(rand(2, 3))->pluck('id')->toArray()));
    }
}
