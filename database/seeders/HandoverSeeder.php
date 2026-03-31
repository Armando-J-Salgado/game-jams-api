<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Team;
use App\Models\Handover;

class HandoverSeeder extends Seeder
{
    public function run(): void
    {
        // Para cada equipo inscrito, registrar entregables
        $equipos = Team::with('competitions.modules')->get();

        foreach ($equipos as $equipo) {
            foreach ($equipo->competitions as $competencia) {
                // Generar Handover por cada módulo de la competencia para este equipo
                foreach ($competencia->modules as $modulo) {
                    Handover::factory()->create([
                        'title' => 'Entrega de ' . $equipo->name . ' - ' . substr($modulo->title, 0, 15),
                        'module_id' => $modulo->id,
                        'team_id' => $equipo->id,
                    ]);
                }
            }
        }
    }
}
