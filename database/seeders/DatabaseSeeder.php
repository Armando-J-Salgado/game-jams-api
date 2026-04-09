<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        $this->call([
            PermissionSeeder::class,
            RoleSeeder::class,
            
            // Orquestador único de usuarios que respeta los roles previamente creados
            UserSeeder::class,
            
            // Catálogo necesario para clasificar competencias
            CategorySeeder::class,
            
            // Competencias manejadas por organizadores
            CompetitionSeeder::class,
            
            // Equipos orquestados por líderes
            TeamSeeder::class,

            // Etapas/Módulos para las competencias
            ModuleSeeder::class,
            
            // Entregas de los equipos en cada módulo
            HandoverSeeder::class,
        ]);
    }
}
