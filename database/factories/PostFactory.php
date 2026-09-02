<?php

namespace Database\Factories;

use App\Enums\Posts\Status;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $dt = fake()->dateTimeBetween('-30 days', 'now');
        $status = Status::cases();
        $users = User::get();

        return [
            'url' => fake()->slug(3),
            'title' => fake()->text(32),
            'content' => fake()->text(),
            'user_id' => $users->random()->id,
            'status' => $status[mt_rand(0, count($status) - 1)],
            'created_at' => $dt,
            'updated_at' => $dt,
        ];
    }
}
