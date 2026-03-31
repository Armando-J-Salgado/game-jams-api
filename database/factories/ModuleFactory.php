<?php

namespace Database\Factories;

use App\Models\Module;
use App\Models\Competition;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Module>
 */
class ModuleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // Título y descripción del módulo o fase de la competencia
            'title' => fake()->sentence(3, true),
            'description' => fake()->paragraph(2, true), // Máx 300 chars prox
            'attachments' => fake()->optional()->url(),
            
            // Fecha de entrega del módulo
            'due_date' => now()->addDays(7),
            
            // Relación: Cada módulo pertenece a una competencia
            'competition_id' => Competition::factory(),
        ];
    }
}
