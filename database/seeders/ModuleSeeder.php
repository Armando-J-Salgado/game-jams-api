<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Competition;
use App\Models\Module;

class ModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Por cada competencia, creamos 3 módulos característicos de un Game Jam
        $competencias = Competition::all();
        
        foreach ($competencias as $competencia) {
            // Módulo 1: Requerimientos
            Module::factory()->create([
                'title' => 'Fase 1: Planteamiento y GDD',
                'description' => 'Documentos de requerimientos y Game Design Document (GDD).',
                'competition_id' => $competencia->id,
                'due_date' => $competencia->start_date ? \Carbon\Carbon::parse($competencia->start_date)->addDays(3) : now()->addDays(3),
            ]);

            // Módulo 2: Primer Avance (Prototipo jugable)
            Module::factory()->create([
                'title' => 'Fase 2: Prototipo Jugable',
                'description' => 'Primera versión de las mecánicas principales.',
                'competition_id' => $competencia->id,
                'due_date' => $competencia->start_date ? \Carbon\Carbon::parse($competencia->start_date)->addDays(7) : now()->addDays(7),
            ]);

            // Módulo 3: Entrega Final
            Module::factory()->create([
                'title' => 'Fase 3: Entrega Final',
                'description' => 'Versión final pulida con audio y gráficos completos.',
                'competition_id' => $competencia->id,
                'due_date' => $competencia->end_date ?? now()->addDays(14),
            ]);
        }
    }
}
