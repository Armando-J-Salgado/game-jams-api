<?php

use App\Models\User;
use App\Models\Team;
use App\Models\Handover;
use App\Models\Module;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    (new PermissionSeeder())->run();
    (new RoleSeeder())->run();
});

// HAPPY PATH

test('It can submit a handover with a valid attachment URL if the user is a team member', function () {
    /** @var \Tests\TestCase $this */
    $leader = User::factory()->create();
    $leader->assignRole('lider');
    $team = Team::factory()->create(['admin_id' => $leader->id]);
    $leader->update(['team_id' => $team->id]);

    $module = Module::factory()->create();
    $handover = Handover::factory()->create([
        'team_id'      => $team->id,
        'module_id'    => $module->id,
        'is_delivered' => false,
        'attachment'   => null,
    ]);

    $this->actingAs($leader, 'sanctum');

    $response = $this->patchJson("api/v1/handovers/{$handover->id}/submit", [
        'attachment' => 'https://github.com/my-team/game-jam-repo',
    ]);

    $response->assertStatus(200)
        ->assertJsonPath('data.is_delivered', true)
        ->assertJsonPath('data.attachment', 'https://github.com/my-team/game-jam-repo')
        ->assertJsonStructure(['message', 'data' => ['id', 'title', 'attachment', 'is_delivered', 'date_of_submission']]);

    $this->assertDatabaseHas('handovers', [
        'id'           => $handover->id,
        'is_delivered' => true,
    ]);
});

// AUTHORIZATION (403)

test('It cannot submit a handover if the user does not belong to the team', function () {
    /** @var \Tests\TestCase $this */
    $outsider = User::factory()->create();
    $outsider->assignRole('participante');

    $team = Team::factory()->create();
    $module = Module::factory()->create();
    $handover = Handover::factory()->create([
        'team_id'   => $team->id,
        'module_id' => $module->id,
    ]);

    $this->actingAs($outsider, 'sanctum');

    $response = $this->patchJson("api/v1/handovers/{$handover->id}/submit", [
        'attachment' => 'https://github.com/outsider/repo',
    ]);

    $response->assertStatus(403);
});

// VALIDATION (422)

test('It cannot submit a handover without an attachment', function () {
    /** @var \Tests\TestCase $this */
    $leader = User::factory()->create();
    $leader->assignRole('lider');
    $team = Team::factory()->create(['admin_id' => $leader->id]);
    $leader->update(['team_id' => $team->id]);

    $module = Module::factory()->create();
    $handover = Handover::factory()->create(['team_id' => $team->id, 'module_id' => $module->id]);

    $this->actingAs($leader, 'sanctum');

    $response = $this->patchJson("api/v1/handovers/{$handover->id}/submit", []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['attachment']);
});

test('It cannot submit a handover with an invalid URL', function () {
    /** @var \Tests\TestCase $this */
    $leader = User::factory()->create();
    $leader->assignRole('lider');
    $team = Team::factory()->create(['admin_id' => $leader->id]);
    $leader->update(['team_id' => $team->id]);

    $module = Module::factory()->create();
    $handover = Handover::factory()->create(['team_id' => $team->id, 'module_id' => $module->id]);

    $this->actingAs($leader, 'sanctum');

    $response = $this->patchJson("api/v1/handovers/{$handover->id}/submit", [
        'attachment' => 'not-a-valid-url',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['attachment']);
});

// NOT FOUND (404)

test('It can return a not found error when the handover is not found to submit', function () {
    /** @var \Tests\TestCase $this */
    $user = User::factory()->create();
    $user->assignRole('lider');
    $this->actingAs($user, 'sanctum');

    $response = $this->patchJson('api/v1/handovers/99999/submit', [
        'attachment' => 'https://github.com/team/repo',
    ]);

    $response->assertStatus(404);
});
