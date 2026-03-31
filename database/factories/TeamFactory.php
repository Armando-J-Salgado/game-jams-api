<?php

namespace Database\Factories;

use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Team>
 */
class TeamFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // Genera nombres únicos estilo empresa o clan para el equipo
            'name' => fake()->unique()->company(),
            
            // Obligatorio: Referencia de admin asumiendo un rol de líder.
            'admin_id' => User::factory()->asLider(),
            
            // Un equipo inicia con 1 miembro (el creador)
            'total_members' => 1,
            'max_members' => 5, // Valor por defecto dictado por migración
        ];
    }
}
