<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            'categories.view',
            'categories.create',
            'categories.update',
            'categories.delete',

            'competitions.view',
            'competitions.create',
            'competitions.update',
            'competitions.delete',

            'handovers.view',
            'handovers.create',
            'handovers.update',
            'handovers.delete',

            'modules.view',
            'modules.create',
            'modules.update',
            'modules.delete',

            'teams.view',
            'teams.create',
            'teams.update',
            'teams.delete',

            'users.view',
            'users.create',
            'users.update',
            'users.delete',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }
    }
    
}
