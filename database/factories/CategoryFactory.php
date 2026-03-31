<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Category>
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
        return [
            // Se asume que una categoría puede abarcar varias palabras, por lo que usamos 'words' en lugar de 'word'
            'name' => fake()->unique()->words(2, true),
        ];
    }
}
