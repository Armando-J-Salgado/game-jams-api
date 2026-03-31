<?php

namespace Database\Factories;

use App\Models\Competition;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Competition>
 */
class CompetitionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // Detalles descriptivos de la competencia
            'name' => fake()->sentence(6, true),
            'description' => fake()->paragraph(3, true), // Máx 500 cars aprox
            'prize_information' => fake()->sentence(6, true),
            'tools_information' => fake()->sentence(6, true),
            
            // Límites y estadísticas (total_teams inicia en 0)
            'max_teams' => fake()->numberBetween(10, 50),
            'total_teams' => 0,
            
            // Fechas relativas por defecto
            'start_date' => now(),
            'end_date' => now()->addDays(14),
            'is_finished' => false,
            
            // Relaciones: Crea categorías y organizadores asociados en caso de no ser provisto durante la inyección.
            'category_id' => Category::factory(),
            'admin_id' => User::factory()->afterCreating(function (User $user) {
                $user->assignRole(fake()->randomElement(['administrador', 'organizador']));
            }),
        ];
    }
}
