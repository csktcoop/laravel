<?php

namespace Database\Factories;

use App\Models\User;
use DDD\Task\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define or resolved into App\Task
     */
    protected $model = Task::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(4),
            'description' => fake()->text(),
            'status' => fake()->randomElement(['todo', 'in_progress', 'done']),
            'assigned_to' => User::factory(),
            'due_date' => fake()->optional()->dateTimeBetween('+1 day', '+30 days'),
        ];
    }
}
