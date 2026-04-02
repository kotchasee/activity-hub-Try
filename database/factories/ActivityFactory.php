<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Activity>
 */
class ActivityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'date' => now()->addDays(5),
            'registration_deadline' => now()->addDays(3),
            'location' => fake()->city(),
            'max_participants' => 20,
            'status' => 'pending',
            'view_count' => 0,
            'user_id' => \App\Models\User::factory(),
        ];
    }
}
