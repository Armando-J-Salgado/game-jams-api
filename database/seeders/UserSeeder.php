<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Administrador
        User::factory()->asAdministrador()->create([
            'name' => 'Administrador',
            'lastname' => 'Inador',
            'username' => 'admin',
            'dui' => '00000000-1',
            'email' => 'admin@gamejam.test',
            // El campo password ya se asigna como 'password' mediante la Factory
        ]);

        // Organizador
        User::factory()->asOrganizador()->create([
            'name' => 'Organizador',
            'lastname' => 'Compulsivo',
            'username' => 'organizador',
            'dui' => '00000000-2',
            'email' => 'organizador@gamejam.test',
        ]);

        // Líder
        User::factory()->asLider()->create([
            'name' => 'Líder',
            'lastname' => 'de la Manada',
            'username' => 'lider',
            'dui' => '00000000-3',
            'email' => 'lider@gamejam.test',
        ]);

        // Participante
        User::factory()->asParticipante()->create([
            'name' => 'Premio',
            'lastname' => 'de Participante',
            'username' => 'participante',
            'dui' => '00000000-4',
            'email' => 'participante@gamejam.test',
        ]);
        
        // Adicional: Crear algunos usuarios genéricos de relleno por defecto
        User::factory(10)->asParticipante()->create();
    }
}
