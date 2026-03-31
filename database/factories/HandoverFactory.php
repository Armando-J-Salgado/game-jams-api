<?php

namespace Database\Factories;

use App\Models\Handover;
use App\Models\Module;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Handover>
 */
class HandoverFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Determinamos primero si habrá archivo (20% de las veces)
        $hasAttachment = fake()->boolean(20);

        return [
            'title' => fake()->sentence(6, true),
            
            // Solo si hay archivo, colocamos una url y marcamos covers como true
            'attachment' => $hasAttachment ? fake()->url() : null,
            'is_delivered' => $hasAttachment,
            
            // Solo calificamos si ya se entregó
            'score' => $hasAttachment ? fake()->numberBetween(1, 10) : null,
            
            // Por defecto, lo podemos rellenar. Se recomienda sobreescribirlo en Seeders
            // para asociarlo correctamente.
            'module_id' => Module::factory(),
            'team_id' => Team::factory(),
        ];
    }
}
