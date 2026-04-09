<?php

use App\Models\User;
use App\Models\Team;
use App\Models\Competition;
use App\Models\Module;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    (new PermissionSeeder())->run();
    (new RoleSeeder())->run();
});

// Helper to build a valid open competition
function openCompetition(): Competition
{
    return Competition::factory()->create([
        'start_date'  => now()->toDateString(),
        'end_date'    => now()->addDays(14)->toDateString(),
        'is_finished' => false,
        'total_teams' => 0,
        'max_teams'   => 10,
    ]);
}

// HAPPY PATH

test('It can enroll a team in a competition if the user is the leader', function () {
    /** @var \Tests\TestCase $this */
    $leader = User::factory()->create();
    $leader->assignRole('lider');
    $team = Team::factory()->create(['admin_id' => $leader->id]);
    $competition = openCompetition();

    $this->actingAs($leader, 'sanctum');

    $response = $this->postJson("api/v1/teams/{$team->id}/enroll/{$competition->id}");

    $response->assertStatus(201)
             ->assertJsonStructure(['data' => ['team', 'competition', 'modules']]);

    $this->assertDatabaseHas('competition_team', [
        'team_id'        => $team->id,
        'competition_id' => $competition->id,
    ]);
});

test('It can create handovers for each module when enrolling a team', function () {
    /** @var \Tests\TestCase $this */
    $leader = User::factory()->create();
    $leader->assignRole('lider');
    $team = Team::factory()->create(['admin_id' => $leader->id]);
    $competition = openCompetition();
    Module::factory(2)->create(['competition_id' => $competition->id]);

    $this->actingAs($leader, 'sanctum');

    $this->postJson("api/v1/teams/{$team->id}/enroll/{$competition->id}");

    $this->assertDatabaseCount('handovers', 2);
});

// AUTHORIZATION (403)

test('It cannot enroll another team in a competition if the user is not the leader', function () {
    /** @var \Tests\TestCase $this */
    $intruder = User::factory()->create();
    $intruder->assignRole('lider');
    $team = Team::factory()->create(); // different admin
    $competition = openCompetition();

    $this->actingAs($intruder, 'sanctum');

    $response = $this->postJson("api/v1/teams/{$team->id}/enroll/{$competition->id}");

    $response->assertStatus(403);
});

// BUSINESS RULES (400)

test('It cannot enroll a team that is already enrolled', function () {
    /** @var \Tests\TestCase $this */
    $leader = User::factory()->create();
    $leader->assignRole('lider');
    $team = Team::factory()->create(['admin_id' => $leader->id]);
    $competition = openCompetition();
    $competition->teams()->attach($team->id);

    $this->actingAs($leader, 'sanctum');

    $response = $this->postJson("api/v1/teams/{$team->id}/enroll/{$competition->id}");

    $response->assertStatus(400);
});

test('It cannot enroll a team when competition is at max capacity', function () {
    /** @var \Tests\TestCase $this */
    $leader = User::factory()->create();
    $leader->assignRole('lider');
    $team = Team::factory()->create(['admin_id' => $leader->id]);
    $competition = openCompetition();
    $competition->update(['total_teams' => $competition->max_teams]);

    $this->actingAs($leader, 'sanctum');

    $response = $this->postJson("api/v1/teams/{$team->id}/enroll/{$competition->id}");

    $response->assertStatus(400);
});

test('It cannot enroll a team before competition registration opens', function () {
    /** @var \Tests\TestCase $this */
    $leader = User::factory()->create();
    $leader->assignRole('lider');
    $team = Team::factory()->create(['admin_id' => $leader->id]);
    $competition = Competition::factory()->create([
        'start_date'  => now()->addDays(5)->toDateString(),
        'end_date'    => now()->addDays(14)->toDateString(),
        'is_finished' => false,
    ]);

    $this->actingAs($leader, 'sanctum');

    $response = $this->postJson("api/v1/teams/{$team->id}/enroll/{$competition->id}");

    $response->assertStatus(400);
});

test('It cannot enroll a team in a finished competition', function () {
    /** @var \Tests\TestCase $this */
    $leader = User::factory()->create();
    $leader->assignRole('lider');
    $team = Team::factory()->create(['admin_id' => $leader->id]);
    $competition = Competition::factory()->create([
        'start_date'  => now()->subDays(14)->toDateString(),
        'end_date'    => now()->subDays(1)->toDateString(),
        'is_finished' => true,
    ]);

    $this->actingAs($leader, 'sanctum');

    $response = $this->postJson("api/v1/teams/{$team->id}/enroll/{$competition->id}");

    $response->assertStatus(400);
});

// NOT FOUND (404)

test('It cannot enroll a team that is not found', function () {
    /** @var \Tests\TestCase $this */
    $user = User::factory()->create();
    $user->assignRole('lider');
    $competition = openCompetition();
    $this->actingAs($user, 'sanctum');

    $response = $this->postJson("api/v1/teams/99999/enroll/{$competition->id}");

    $response->assertStatus(404);
});
