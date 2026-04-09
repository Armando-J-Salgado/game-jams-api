<?php

use App\Models\User;
use App\Models\Team;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    (new PermissionSeeder())->run();
    (new RoleSeeder())->run();
});

// INDEX TESTS

test('It can list all teams', function (string $role) {
    /** @var \Tests\TestCase $this */
    $user = User::factory()->create();
    $user->assignRole($role);
    $this->actingAs($user, 'sanctum');

    Team::factory(3)->create();

    $response = $this->getJson('api/v1/teams');

    $response->assertStatus(200)
        ->assertJsonCount(3, 'data');
})->with(['administrador', 'organizador', 'lider', 'participante']);

test('It cannot list teams to unauthenticated users', function () {
    /** @var \Tests\TestCase $this */
    $response = $this->getJson('api/v1/teams');

    $response->assertStatus(401);
});

// STORE TESTS

test('It can create a team', function () {
    /** @var \Tests\TestCase $this */
    $leader = User::factory()->create();
    $leader->assignRole('lider');
    $this->actingAs($leader, 'sanctum');

    $response = $this->postJson('api/v1/teams', [
        'name'     => 'Equipo Alfa',
        'admin_id' => $leader->id,
    ]);

    $response->assertStatus(201)
        ->assertJsonPath('data.name', 'Equipo Alfa')
        ->assertJsonPath('data.total_members', 1)
        ->assertJsonPath('data.max_members', 5);

    $this->assertDatabaseHas('teams', ['name' => 'Equipo Alfa']);
});

test('It cannot create a team with an invalid role', function (string $role) {
    /** @var \Tests\TestCase $this */
    $user = User::factory()->create();
    $user->assignRole($role);
    $this->actingAs($user, 'sanctum');

    $response = $this->postJson('api/v1/teams', [
        'name'     => 'Equipo Secreto',
        'admin_id' => $user->id,
    ]);

    $response->assertStatus(403);
})->with(['participante']);

test('It cannot create a team with missing required fields', function () {
    /** @var \Tests\TestCase $this */
    $leader = User::factory()->create();
    $leader->assignRole('lider');
    $this->actingAs($leader, 'sanctum');

    $response = $this->postJson('api/v1/teams', []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name', 'admin_id']);
});

test('It cannot create a team with a duplicate name', function () {
    /** @var \Tests\TestCase $this */
    $leader = User::factory()->create();
    $leader->assignRole('lider');
    $this->actingAs($leader, 'sanctum');

    Team::factory()->create(['name' => 'Equipo Existente']);

    $response = $this->postJson('api/v1/teams', [
        'name'     => 'Equipo Existente',
        'admin_id' => $leader->id,
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name']);
});

// SHOW TESTS

test('It can show a specific team', function () {
    /** @var \Tests\TestCase $this */
    $user = User::factory()->create();
    $user->assignRole('administrador');
    $this->actingAs($user, 'sanctum');

    $team = Team::factory()->create();

    $response = $this->getJson("api/v1/teams/{$team->id}");

    $response->assertStatus(200)
        ->assertJsonPath('data.id', $team->id)
        ->assertJsonPath('data.name', $team->name);
});

test('It returns 404 when showing a non-existent team', function () {
    /** @var \Tests\TestCase $this */
    $user = User::factory()->create();
    $user->assignRole('administrador');
    $this->actingAs($user, 'sanctum');

    $response = $this->getJson('api/v1/teams/99999');

    $response->assertStatus(404);
});

// UPDATE TESTS

test('It can update a team', function () {
    /** @var \Tests\TestCase $this */
    $user = User::factory()->create();
    $user->assignRole('administrador');
    $this->actingAs($user, 'sanctum');

    $team = Team::factory()->create();

    $response = $this->putJson("api/v1/teams/{$team->id}", [
        'name' => 'Equipo Actualizado',
    ]);

    $response->assertStatus(200)
        ->assertJsonPath('data.name', 'Equipo Actualizado');

    $this->assertDatabaseHas('teams', ['id' => $team->id, 'name' => 'Equipo Actualizado']);
});

test('It returns 404 when updating a non-existent team', function () {
    /** @var \Tests\TestCase $this */
    $user = User::factory()->create();
    $user->assignRole('administrador');
    $this->actingAs($user, 'sanctum');

    $response = $this->putJson('api/v1/teams/99999', ['name' => 'Ghost Team']);

    $response->assertStatus(404);
});

// DESTROY (SOFT DELETE) TESTS

test('It can soft delete a team', function () {
    /** @var \Tests\TestCase $this */
    $user = User::factory()->create();
    $user->assignRole('administrador');
    $this->actingAs($user, 'sanctum');

    $team = Team::factory()->create();

    $response = $this->deleteJson("api/v1/teams/{$team->id}");

    $response->assertStatus(204);
    $this->assertSoftDeleted('teams', ['id' => $team->id]);
});

test('It returns 404 when deleting a non-existent team', function () {
    /** @var \Tests\TestCase $this */
    $user = User::factory()->create();
    $user->assignRole('administrador');
    $this->actingAs($user, 'sanctum');

    $response = $this->deleteJson('api/v1/teams/99999');

    $response->assertStatus(404);
});

// DELETED & RESTORE TESTS

test('It can list soft-deleted teams', function () {
    /** @var \Tests\TestCase $this */
    $user = User::factory()->create();
    $user->assignRole('administrador');
    $this->actingAs($user, 'sanctum');

    $team = Team::factory()->create();
    $team->delete();

    $response = $this->getJson('api/v1/teams/deleted');

    $response->assertStatus(200)
        ->assertJsonCount(1, 'data');
});

test('It can restore a soft-deleted team', function () {
    /** @var \Tests\TestCase $this */
    $user = User::factory()->create();
    $user->assignRole('administrador');
    $this->actingAs($user, 'sanctum');

    $team = Team::factory()->create();
    $team->delete();

    $response = $this->patchJson("api/v1/teams/{$team->id}/restore");

    $response->assertStatus(200)
        ->assertJsonPath('data.id', $team->id);

    $this->assertDatabaseHas('teams', ['id' => $team->id, 'deleted_at' => null]);
});

test('It returns 404 when restoring a non-existent soft-deleted team', function () {
    /** @var \Tests\TestCase $this */
    $user = User::factory()->create();
    $user->assignRole('administrador');
    $this->actingAs($user, 'sanctum');

    $response = $this->patchJson('api/v1/teams/99999/restore');

    $response->assertStatus(404);
});
