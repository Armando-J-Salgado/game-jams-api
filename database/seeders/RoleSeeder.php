<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;


class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $administrador = Role::firstOrCreate(['name' => 'administrador', 'guard_name' => 'web']);
        $organizador = Role::firstOrCreate(['name' => 'organizador', 'guard_name' => 'web']);
        $lider = Role::firstOrCreate(['name' => 'lider', 'guard_name' => 'web']);
        $participante = Role::firstOrCreate(['name' => 'participante', 'guard_name' => 'web']);

            $administrador->syncPermissions([
                'categories.view',
                'categories.create',
                'categories.update',
                'categories.delete',
    
                'competitions.view',
                'competitions.create',
                'competitions.update',
                'competitions.delete',
                'competitions.restore',
    
                'handovers.view',
                'handovers.create',
                'handovers.update',
                'handovers.delete',
    
                'modules.view',
                'modules.create',
                'modules.update',
                'modules.delete',
                'modules.restore',
    
                'teams.view',
                'teams.create',
                'teams.update',
                'teams.delete',
    
                'users.view',
                'users.create',
                'users.update',
                'users.delete',
            ]);

            $organizador->syncPermissions([
                'categories.view',
    
                'competitions.view',
                'competitions.create',
                'competitions.update',
    
                'handovers.view',
                'handovers.create',
                'handovers.update',
    
                'modules.view',
                'modules.create',
                'modules.update',

                'users.view',
            ]);

            $lider->syncPermissions([

                'categories.view',

                'competitions.view',

                'handovers.view',
                'handovers.create',
                'handovers.update',

                'modules.view',

                'teams.view',
                'teams.create',
                'teams.update',

                'users.view',
            ]);

            $participante->syncPermissions([
                'categories.view',

                'competitions.view',

                'handovers.view',
                'handovers.create',
                'handovers.update',

                'modules.view',

                'teams.view',

                'users.view',
            ]);
    }
        
}
