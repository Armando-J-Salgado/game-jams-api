<?php

use App\Models\User;
use App\Models\Team;
use App\Models\Competition;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    (new PermissionSeeder())->run();
    (new RoleSeeder())->run();
});

// HAPPY PATH

test('A leader can withdraw their team from a competition', function () {
    /** @var \Tests\TestCase $this */
    $leader = User::factory()->create();
    $leader->assignRole('lider');
    $team = Team::factory()->create(['admin_id' => $leader->id]);
    $competition = Competition::factory()->create(['total_teams' => 1]);
    $competition->teams()->attach($team->id);

    $this->actingAs($leader, 'sanctum');

    $response = $this->deleteJson("api/v1/teams/{$team->id}/withdraw/{$competition->id}");

    $response->assertStatus(200)
        ->assertJsonPath('message', 'Team withdrawn successfully from the competition. Handovers are kept as history.')
        ->assertJsonStructure(['data' => ['id', 'name', 'total_members']]);

    $this->assertDatabaseMissing('competition_team', [
        'team_id'        => $team->id,
        'competition_id' => $competition->id,
    ]);
});

test('Withdrawing decrements the competition total_teams count', function () {
    /** @var \Tests\TestCase $this */
    $leader = User::factory()->create();
    $leader->assignRole('lider');
    $team = Team::factory()->create(['admin_id' => $leader->id]);
    $competition = Competition::factory()->create(['total_teams' => 1]);
    $competition->teams()->attach($team->id);

    $this->actingAs($leader, 'sanctum');

    $this->deleteJson("api/v1/teams/{$team->id}/withdraw/{$competition->id}");

    $this->assertDatabaseHas('competitions', [
        'id'          => $competition->id,
        'total_teams' => 0,
    ]);
});

// AUTHORIZATION (403)

test('A non-leader cannot withdraw another team from a competition', function () {
    /** @var \Tests\TestCase $this */
    $intruder = User::factory()->create();
    $intruder->assignRole('lider');
    $team = Team::factory()->create(); // different admin
    $competition = Competition::factory()->create();
    $competition->teams()->attach($team->id);

    $this->actingAs($intruder, 'sanctum');

    $response = $this->deleteJson("api/v1/teams/{$team->id}/withdraw/{$competition->id}");

    $response->assertStatus(403);
});

// BUSINESS RULES (400)

test('Cannot withdraw a team that is not enrolled', function () {
    /** @var \Tests\TestCase $this */
    $leader = User::factory()->create();
    $leader->assignRole('lider');
    $team = Team::factory()->create(['admin_id' => $leader->id]);
    $competition = Competition::factory()->create();

    $this->actingAs($leader, 'sanctum');

    $response = $this->deleteJson("api/v1/teams/{$team->id}/withdraw/{$competition->id}");

    $response->assertStatus(400);
});

// NOT FOUND (404)

test('Returns 404 when team is not found on withdraw', function () {
    /** @var \Tests\TestCase $this */
    $user = User::factory()->create();
    $user->assignRole('lider');
    $competition = Competition::factory()->create();
    $this->actingAs($user, 'sanctum');

    $response = $this->deleteJson("api/v1/teams/99999/withdraw/{$competition->id}");

    $response->assertStatus(404);
});
