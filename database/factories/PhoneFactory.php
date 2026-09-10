<?php

namespace Database\Factories;

use App\Models\Phone;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Phone>
 */
class PhoneFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $dt = fake()->dateTimeBetween('-30 days');
        return [
            'phone' => fake()->unique()->numerify('79#########'),
            'verified_at' => $dt,
        ];
    }
}
