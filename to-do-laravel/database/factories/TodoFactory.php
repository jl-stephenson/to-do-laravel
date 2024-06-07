<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Todo>
 */
class TodoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'completed' => $this->faker->numberBetween(0, 1),
            'created_at' => $this->faker->unixTime(),
            'updated_at' => $this->faker->unixTime(),
            'category_id' => Category::factory()

        ];
    }
}
