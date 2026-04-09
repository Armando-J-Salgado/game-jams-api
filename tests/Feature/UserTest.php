<?php

use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed([
        PermissionSeeder::class,
        RoleSeeder::class,
    ]);
});

dataset('restrictedRoles', [
    'organizador',
    'lider',
    'participante',
]);


// Test # it blocks users listing without auth
it('it cannot list users without authentication', function () {
    $this->getJson('/api/v1/users')
        ->assertStatus(401);
});

// Test # it lists users for admin
it('it can list users as administrador', function () {
    $admin = User::factory()->asAdministrador()->create();
    User::factory()->count(2)->create();

    Sanctum::actingAs($admin);

    $this->getJson('/api/v1/users')
        ->assertOk()
        ->assertJsonStructure([
            'data',
        ]);
});

// Test # it cannot list users for non admin
it('it cannot list users as non administrador', function (string $role) {
    $actor = User::factory()->create();
    $actor->assignRole($role);

    Sanctum::actingAs($actor);

    $this->getJson('/api/v1/users')
        ->assertStatus(403);
})->with('restrictedRoles');

// Test # it cannot create users without permission
it('it cannot create users without authorization', function (string $role) {
    $actor = User::factory()->create();
    $actor->assignRole($role);

    Sanctum::actingAs($actor);

    $payload = [
        'name' => 'Maria',
        'lastname' => 'Lopez',
        'email' => 'maria.' . $role . '@example.com',
        'username' => 'mlopez_' . $role,
        'password' => 'password123',
        'dui' => '12345678-9',
        'role' => 'organizador',
    ];

    $this->postJson('/api/v1/users', $payload)
        ->assertStatus(403);
})->with('restrictedRoles');

// Test # it cannot create with duplicate email
it('it cannot create a user with duplicate email', function () {
    $admin = User::factory()->asAdministrador()->create();
    $existing = User::factory()->create();

    Sanctum::actingAs($admin);

    $payload = [
        'name' => 'Maria',
        'lastname' => 'Lopez',
        'email' => $existing->email,
        'username' => 'mlopez',
        'password' => 'password123',
        'dui' => '12345678-9',
        'role' => 'organizador',
    ];

    $this->postJson('/api/v1/users', $payload)
        ->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});

// Test # it cannot create with duplicate dui
it('it cannot create a user with duplicate dui', function () {
    $admin = User::factory()->asAdministrador()->create();
    $existing = User::factory()->create();

    Sanctum::actingAs($admin);

    $payload = [
        'name' => 'Maria',
        'lastname' => 'Lopez',
        'email' => 'maria.lopez@example.com',
        'username' => 'mlopez',
        'password' => 'password123',
        'dui' => $existing->dui,
        'role' => 'organizador',
    ];

    $this->postJson('/api/v1/users', $payload)
        ->assertStatus(422)
        ->assertJsonValidationErrors(['dui']);
});

// Test # it cannot create with invalid dui format
it('it cannot create a user with invalid dui format', function () {
    $admin = User::factory()->asAdministrador()->create();

    Sanctum::actingAs($admin);

    $payload = [
        'name' => 'Maria',
        'lastname' => 'Lopez',
        'email' => 'maria.lopez@example.com',
        'username' => 'mlopez',
        'password' => 'password123',
        'dui' => '1234',
        'role' => 'organizador',
    ];

    $this->postJson('/api/v1/users', $payload)
        ->assertStatus(422)
        ->assertJsonValidationErrors(['dui']);
});

// Test # it cannot create with invalid role
it('it cannot create a user with invalid role', function () {
    $admin = User::factory()->asAdministrador()->create();

    Sanctum::actingAs($admin);

    $payload = [
        'name' => 'Maria',
        'lastname' => 'Lopez',
        'email' => 'maria.lopez@example.com',
        'username' => 'mlopez',
        'password' => 'password123',
        'dui' => '12345678-9',
        'role' => 'rol-invalido',
    ];

    $this->postJson('/api/v1/users', $payload)
        ->assertStatus(422)
        ->assertJsonValidationErrors(['role']);
});

// Test # it creates a user with role
it('it can create a user with role', function () {
    $admin = User::factory()->asAdministrador()->create();
    Sanctum::actingAs($admin);

    $payload = [
        'name' => 'Maria',
        'lastname' => 'Lopez',
        'email' => 'maria.lopez@example.com',
        'username' => 'mlopez',
        'password' => 'password123',
        'dui' => '12345678-9',
        'role' => 'organizador',
    ];

    $this->postJson('/api/v1/users', $payload)
        ->assertStatus(201)
        ->assertJsonPath('data.email', $payload['email']);

    $created = User::where('email', $payload['email'])->first();
    expect($created)->not->toBeNull();
    expect($created->hasRole('organizador'))->toBeTrue();
});

// Test # it shows a user
it('it can show any user as administrador', function () {
    $admin = User::factory()->asAdministrador()->create();
    $user = User::factory()->create();

    Sanctum::actingAs($admin);

    $this->getJson('/api/v1/users/' . $user->id)
        ->assertOk()
        ->assertJsonPath('data.id', $user->id);
});

// Test # it shows own profile
it('it can show own profile', function (string $role) {
    $user = User::factory()->create();
    $user->assignRole($role);

    Sanctum::actingAs($user);

    $this->getJson('/api/v1/users/' . $user->id)
        ->assertOk()
        ->assertJsonPath('data.id', $user->id);
})->with('restrictedRoles');

// Test # it cannot show another user profile
it('it cannot show another user profile', function (string $role) {
    $actor = User::factory()->create();
    $actor->assignRole($role);
    $other = User::factory()->create();

    Sanctum::actingAs($actor);

    $this->getJson('/api/v1/users/' . $other->id)
        ->assertStatus(403);
})->with('restrictedRoles');

// Test # it updates a user
it('it can update any user as administrator', function () {
    $admin = User::factory()->asAdministrador()->create();
    $user = User::factory()->create();

    Sanctum::actingAs($admin);

    $payload = [
        'name' => 'Carlos',
        'lastname' => 'Perez',
        'role' => 'lider',
    ];

    $this->putJson('/api/v1/users/' . $user->id, $payload)
        ->assertOk()
        ->assertJsonPath('data.name', $payload['name']);

    $user->refresh();
    expect($user->hasRole('lider'))->toBeTrue();
});

// Test # it cannot update with duplicate email
it('it cannot update a user with duplicate email', function () {
    $admin = User::factory()->asAdministrador()->create();
    $existing = User::factory()->create();
    $user = User::factory()->create();

    Sanctum::actingAs($admin);

    $payload = [
        'email' => $existing->email,
        'name' => 'Carlos',
        'lastname' => 'Perez',
    ];

    $this->putJson('/api/v1/users/' . $user->id, $payload)
        ->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});

// Test # it cannot update with duplicate dui
it('it cannot update a user with duplicate dui', function () {
    $admin = User::factory()->asAdministrador()->create();
    $existing = User::factory()->create();
    $user = User::factory()->create();

    Sanctum::actingAs($admin);

    $payload = [
        'dui' => $existing->dui,
        'name' => 'Carlos',
        'lastname' => 'Perez',
    ];

    $this->putJson('/api/v1/users/' . $user->id, $payload)
        ->assertStatus(422)
        ->assertJsonValidationErrors(['dui']);
});

// Test # it cannot update with invalid dui format
it('it cannot update a user with invalid dui format', function () {
    $admin = User::factory()->asAdministrador()->create();
    $user = User::factory()->create();

    Sanctum::actingAs($admin);

    $payload = [
        'dui' => '1234',
        'name' => 'Carlos',
        'lastname' => 'Perez',
    ];

    $this->putJson('/api/v1/users/' . $user->id, $payload)
        ->assertStatus(422)
        ->assertJsonValidationErrors(['dui']);
});

// Test # it cannot update with invalid role
it('it cannot update a user with invalid role', function () {
    $admin = User::factory()->asAdministrador()->create();
    $user = User::factory()->create();

    Sanctum::actingAs($admin);

    $payload = [
        'role' => 'rol-invalido',
        'name' => 'Carlos',
        'lastname' => 'Perez',
    ];

    $this->putJson('/api/v1/users/' . $user->id, $payload)
        ->assertStatus(422)
        ->assertJsonValidationErrors(['role']);
});

// Test # it updates own profile
it('it can update own profile', function (string $role) {
    $user = User::factory()->create();
    $user->assignRole($role);

    Sanctum::actingAs($user);

    $payload = [
        'name' => 'Luis',
        'lastname' => 'Martinez',
        'username' => 'lmartinez_' . $role,
    ];

    $this->putJson('/api/v1/users/' . $user->id, $payload)
        ->assertOk()
        ->assertJsonPath('data.name', $payload['name'])
        ->assertJsonPath('data.username', $payload['username']);
})->with('restrictedRoles');

// Test # it ignores restricted fields on self update
it('it cannot update restricted fields on own profile', function (string $role) {
    $targetRole = $role === 'organizador' ? 'lider' : 'organizador';
    $user = User::factory()->create([
        'email' => 'original.' . $role . '@example.com',
        'dui' => '12345678-9',
    ]);
    $user->assignRole($role);

    Sanctum::actingAs($user);

    $payload = [
        'name' => 'Luis',
        'email' => 'updated.' . $role . '@example.com',
        'dui' => '87654321-0',
        'role' => $targetRole,
    ];

    $this->putJson('/api/v1/users/' . $user->id, $payload)
        ->assertOk()
        ->assertJsonPath('data.name', $payload['name']);

    $user->refresh();
    expect($user->email)->toBe('original.' . $role . '@example.com');
    expect($user->dui)->toBe('12345678-9');
    expect($user->hasRole($role))->toBeTrue();
    expect($user->hasRole($targetRole))->toBeFalse();
})->with('restrictedRoles');

// Test # it cannot update another user profile
it('it cannot update another user profile', function (string $role) {
    $actor = User::factory()->create();
    $actor->assignRole($role);
    $other = User::factory()->create();

    Sanctum::actingAs($actor);

    $payload = [
        'name' => 'Luis',
        'lastname' => 'Martinez',
    ];

    $this->putJson('/api/v1/users/' . $other->id, $payload)
        ->assertStatus(403);
})->with('restrictedRoles');

// Test # it cannot update users without permission
it('it cannot update users without authorization', function (string $role) {
    $actor = User::factory()->create();
    $actor->assignRole($role);

    $other = User::factory()->create(); 

    Sanctum::actingAs($actor);

    $this->putJson('/api/v1/users/' . $other->id, [ 
        'name' => 'Carlos',
        'lastname' => 'Perez',
    ])->assertStatus(403);
})->with('restrictedRoles');

// Test # it deletes a user
it('it can delete a user as administrator', function () {
    $admin = User::factory()->asAdministrador()->create();
    $user = User::factory()->create();

    Sanctum::actingAs($admin);

    $this->deleteJson('/api/v1/users/' . $user->id)
        ->assertStatus(200);
    $this->assertSoftDeleted('users', [
        'id' => $user->id,
    ]);
});

// Test # it cannot delete users without permission
it('it cannot delete users without permission', function (string $role) {
    $actor = User::factory()->create();
    $actor->assignRole($role);

    $user = User::factory()->create();

    Sanctum::actingAs($actor);

    $this->deleteJson('/api/v1/users/' . $user->id)
        ->assertStatus(403);
})->with('restrictedRoles');

// Test # it filters users by deleted status
it('it can filter users by deleted status', function () {
    $admin = User::factory()->asAdministrador()->create();
    $activeUser = User::factory()->create();
    $deletedUser = User::factory()->create();
    $deletedUser->delete();

    Sanctum::actingAs($admin);

    $response = $this->getJson('/api/v1/users?status=deleted')
        ->assertOk()
        ->assertJsonStructure(['data']);

    $ids = collect($response->json('data'))->pluck('id');

    expect($ids)->toContain($deletedUser->id);
    expect($ids)->not->toContain($activeUser->id);
    expect($ids)->not->toContain($admin->id);
});

// Test # it filters users by active status
it('it can filter users by active status', function () {
    $admin = User::factory()->asAdministrador()->create();
    $activeUser = User::factory()->create();
    $deletedUser = User::factory()->create();
    $deletedUser->delete();

    Sanctum::actingAs($admin);

    $response = $this->getJson('/api/v1/users?status=active')
        ->assertOk()
        ->assertJsonStructure(['data']);

    $ids = collect($response->json('data'))->pluck('id');

    expect($ids)->toContain($admin->id);
    expect($ids)->toContain($activeUser->id);
    expect($ids)->not->toContain($deletedUser->id);
});

// Test # it filters users by all status
it('it can filter users by all status', function () {
    $admin = User::factory()->asAdministrador()->create();
    $activeUser = User::factory()->create();
    $deletedUser = User::factory()->create();
    $deletedUser->delete();

    Sanctum::actingAs($admin);

    $response = $this->getJson('/api/v1/users?status=all')
        ->assertOk()
        ->assertJsonStructure(['data']);

    $ids = collect($response->json('data'))->pluck('id');

    expect($ids)->toContain($admin->id);
    expect($ids)->toContain($activeUser->id);
    expect($ids)->toContain($deletedUser->id);
});

// Test # it restores a soft deleted user
it('it can restore a soft deleted user as administrator', function () {
    $admin = User::factory()->asAdministrador()->create();
    $user = User::factory()->create();
    $user->delete();

    Sanctum::actingAs($admin);

    $this->patchJson('/api/v1/users/' . $user->id . '/restore')
        ->assertStatus(200)
        ->assertJsonPath('message', 'User restored successfully');

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'deleted_at' => null,
    ]);
});

// Test # it permanently deletes a soft deleted user
it('it can permanently delete a soft deleted user as administrator', function () {
    $admin = User::factory()->asAdministrador()->create();
    $user = User::factory()->create();
    $user->delete();

    Sanctum::actingAs($admin);

    $this->deleteJson('/api/v1/users/' . $user->id . '/force')
        ->assertStatus(200)
        ->assertJsonPath('message', 'User permanently deleted successfully');

    $this->assertDatabaseMissing('users', [
        'id' => $user->id,
    ]);
});

// Test # it cannot restore users without permission
it('it cannot restore users without authorization', function (string $role) {
    $actor = User::factory()->create();
    $actor->assignRole($role);

    $user = User::factory()->create();
    $user->delete();

    Sanctum::actingAs($actor);

    $this->patchJson('/api/v1/users/' . $user->id . '/restore')
        ->assertStatus(403);
})->with('restrictedRoles');

// Test # it cannot permanently delete users without permission
it('it cannot permanently delete users without authorization', function (string $role) {
    $actor = User::factory()->create();
    $actor->assignRole($role);

    $user = User::factory()->create();
    $user->delete();

    Sanctum::actingAs($actor);

    $this->deleteJson('/api/v1/users/' . $user->id . '/force')
        ->assertStatus(403);
})->with('restrictedRoles');
