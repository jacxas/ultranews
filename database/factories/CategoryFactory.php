<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Category>
 */
class CategoryFactory extends Factory
{
    protected $model = Category::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = Str::title(fake()->words(2, true));

        return [
            'name' => $name,
            // El slug único se genera automáticamente en el evento saving del modelo.
            'description' => fake()->sentence(8),
            'color' => fake()->randomElement(['#e11d48', '#f97316', '#22d3ee', '#a855f7', '#84cc16', '#eab308']),
            'sort_order' => fake()->numberBetween(0, 10),
        ];
    }
}
