<?php

namespace Database\Seeders;

use App\Models\Competition;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CompetitionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener los usuarios base que creamos en UserSeeder
        $organizador = User::where('email', 'organizador@gamejam.test')->first();
        $administrador = User::where('email', 'admin@gamejam.test')->first();

        // Competencias para el organizador base
        Competition::factory(2)->create([
            'admin_id' => $organizador ? $organizador->id : User::factory()->asOrganizador(),
        ]);

        // Competencias para el administrador base
        Competition::factory(2)->create([
            'admin_id' => $administrador ? $administrador->id : User::factory()->asAdministrador(),
        ]);
        
        // Competencias adicionales con creadores aleatorios (administradores u organizadores dependiente de la Factory)
        Competition::factory(2)->create();
    }
}
