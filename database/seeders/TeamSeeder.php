<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Team;
use App\Models\Competition;
use Illuminate\Database\Seeder;

class TeamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener al líder principal y participante estático
        $lider = User::where('email', 'lider@gamejam.test')->first();
        $participante = User::where('email', 'participante@gamejam.test')->first();

        // 1. Crear el equipo oficial de nuestro líder "testeador"
        $equipoOficial = Team::factory()->create([
            'admin_id' => $lider ? $lider->id : User::factory()->asLider(),
        ]);

        // Aseguramos que el participante es parte de su equipo y ajustamos la cantidad de miembros
        if ($participante) {
            $participante->team_id = $equipoOficial->id;
            $participante->save();

            $equipoOficial->total_members = 2; // líder + participante
            $equipoOficial->save();
        }

        // 2. Asociar el equipo a alguna competencia existente (si existen)
        $competencia = Competition::inRandomOrder()->first();
        if ($competencia) {
            $equipoOficial->competitions()->attach($competencia->id);
            // Actualizar el total_teams de la competencia según la migración y regla de negocio
            $competencia->increment('total_teams'); 
        }

        // 3. Generar equipos de relleno para visualizar listas llenas
        Team::factory(5)->create()->each(function (Team $team) {
            // Relacionar a competencias (cada uno se asocia a entre 1 a 2 competencias aleatorias)
            $compids = Competition::inRandomOrder()->limit(rand(1, 2))->pluck('id');
            $team->competitions()->attach($compids);
            
            Competition::whereIn('id', $compids)->increment('total_teams');
        });
    }
}
