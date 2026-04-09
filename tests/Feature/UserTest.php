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

dataset('duplicateUserFields', [
    'email',
    'dui',
]);

// Test # it blocks users listing without auth
it('blocks users listing without auth', function () {
    $this->getJson('/api/v1/users')
        ->assertStatus(401);
});

// Test # it lists users for admin
it('lists users for admin', function () {
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
it('cannot list users for non admin', function (string $role) {
    $actor = User::factory()->create();
    $actor->assignRole($role);

    Sanctum::actingAs($actor);

    $this->getJson('/api/v1/users')
        ->assertStatus(403);
})->with('restrictedRoles');

// Test # it cannot create users without permission
it('cannot create users without permission', function (string $role) {
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

// Test # it cannot create with duplicate fields
it('cannot create with duplicate fields', function (string $field) {
    $admin = User::factory()->asAdministrador()->create();
    $existing = User::factory()->create();

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

    if ($field === 'email') {
        $payload['email'] = $existing->email;
    }

    if ($field === 'dui') {
        $payload['dui'] = $existing->dui;
    }

    $this->postJson('/api/v1/users', $payload)
        ->assertStatus(422)
        ->assertJsonValidationErrors([$field]);
})->with('duplicateUserFields');

// Test # it creates a user with role
it('creates a user with role', function () {
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
it('shows a user', function () {
    $admin = User::factory()->asAdministrador()->create();
    $user = User::factory()->create();

    Sanctum::actingAs($admin);

    $this->getJson('/api/v1/users/' . $user->id)
        ->assertOk()
        ->assertJsonPath('data.id', $user->id);
});

// Test # it shows own profile
it('shows own profile', function () {
    $user = User::factory()->asParticipante()->create();

    Sanctum::actingAs($user);

    $this->getJson('/api/v1/users/' . $user->id)
        ->assertOk()
        ->assertJsonPath('data.id', $user->id);
});

// Test # it cannot show another user profile
it('cannot show another user profile', function () {
    $actor = User::factory()->asParticipante()->create();
    $other = User::factory()->create();

    Sanctum::actingAs($actor);

    $this->getJson('/api/v1/users/' . $other->id)
        ->assertStatus(403);
});

// Test # it updates a user
it('updates a user', function () {
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

// Test # it updates own profile
it('updates own profile', function () {
    $user = User::factory()->asParticipante()->create();

    Sanctum::actingAs($user);

    $payload = [
        'name' => 'Luis',
        'lastname' => 'Martinez',
    ];

    $this->putJson('/api/v1/users/' . $user->id, $payload)
        ->assertOk()
        ->assertJsonPath('data.name', $payload['name']);
});

// Test # it cannot update another user profile
it('cannot update another user profile', function () {
    $actor = User::factory()->asParticipante()->create();
    $other = User::factory()->create();

    Sanctum::actingAs($actor);

    $payload = [
        'name' => 'Luis',
        'lastname' => 'Martinez',
    ];

    $this->putJson('/api/v1/users/' . $other->id, $payload)
        ->assertStatus(403);
});

// Test # it cannot update users without permission
it('cannot update users without permission', function (string $role) {
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
it('deletes a user', function () {
    $admin = User::factory()->asAdministrador()->create();
    $user = User::factory()->create();

    Sanctum::actingAs($admin);

    $this->deleteJson('/api/v1/users/' . $user->id)
        ->assertStatus(200);

    $this->assertDatabaseMissing('users', [
        'id' => $user->id,
    ]);
});

// Test # it cannot delete users without permission
it('cannot delete users without permission', function (string $role) {
    $actor = User::factory()->create();
    $actor->assignRole($role);

    $user = User::factory()->create();

    Sanctum::actingAs($actor);

    $this->deleteJson('/api/v1/users/' . $user->id)
        ->assertStatus(403);
})->with('restrictedRoles');
