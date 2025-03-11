<?php

namespace Database\Factories;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->name,
            'description' => fake()->text(50),
            'priority' => fake()->randomElement(['medium', 'low', 'high']),
            'repeat' => fake()->randomElement(['daily', 'weekly', 'monthly', 'none']),
            'is_completed' => 0,
            'user_id' => User::first()->id,
            'due_date' => fake()->randomElement([
                Carbon::tomorrow(),
                Carbon::today()
            ]),
        ];
    }
}
