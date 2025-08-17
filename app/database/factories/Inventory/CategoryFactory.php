<?php

namespace Database\Factories\Inventory;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Inventory\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->unique()->name();
        $slug = Str::slug($name);

        return [
            'name' => $name,
            'slug' => $slug,
            // 'parent_id', 'path' set in seeder
            'description' => $this->faker->boolean(90) ? 'cat desc' : null,
        ];
    }
}
