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

// HAPPY PATH

test('It can add a member to the team if the user is the leader', function () {
    /** @var \Tests\TestCase $this */
    $leader = User::factory()->create();
    $leader->assignRole('lider');
    $team = Team::factory()->create(['admin_id' => $leader->id, 'total_members' => 1, 'max_members' => 5]);

    $newMember = User::factory()->create(['team_id' => null]);
    $newMember->assignRole('participante');

    $this->actingAs($leader, 'sanctum');

    $response = $this->postJson("api/v1/teams/{$team->id}/members/{$newMember->id}");

    $response->assertStatus(201)
        ->assertJsonPath('message', 'Member added successfully to the team.')
        ->assertJsonPath('data.total_members', 2);

    $this->assertDatabaseHas('users', [
        'id'      => $newMember->id,
        'team_id' => $team->id,
    ]);
});

test('It can increase the team total_members when adding a member', function () {
    /** @var \Tests\TestCase $this */
    $leader = User::factory()->create();
    $leader->assignRole('lider');
    $team = Team::factory()->create(['admin_id' => $leader->id, 'total_members' => 1, 'max_members' => 5]);
    $newMember = User::factory()->create(['team_id' => null]);

    $this->actingAs($leader, 'sanctum');
    $this->postJson("api/v1/teams/{$team->id}/members/{$newMember->id}");

    $this->assertDatabaseHas('teams', ['id' => $team->id, 'total_members' => 2]);
});

// AUTHORIZATION (403)

test('It cannot add members to another team if the user is not the leader', function () {
    /** @var \Tests\TestCase $this */
    $intruder = User::factory()->create();
    $intruder->assignRole('lider');
    $team = Team::factory()->create(); // different admin
    $newMember = User::factory()->create(['team_id' => null]);

    $this->actingAs($intruder, 'sanctum');

    $response = $this->postJson("api/v1/teams/{$team->id}/members/{$newMember->id}");

    $response->assertStatus(403);
});

// BUSINESS RULES (400)

test('It cannot add a user who already belongs to this team', function () {
    /** @var \Tests\TestCase $this */
    $leader = User::factory()->create();
    $leader->assignRole('lider');
    $team = Team::factory()->create(['admin_id' => $leader->id]);
    $member = User::factory()->create(['team_id' => $team->id]);

    $this->actingAs($leader, 'sanctum');

    $response = $this->postJson("api/v1/teams/{$team->id}/members/{$member->id}");

    $response->assertStatus(400);
});

test('It cannot add a user who already belongs to another team', function () {
    /** @var \Tests\TestCase $this */
    $leader = User::factory()->create();
    $leader->assignRole('lider');
    $team = Team::factory()->create(['admin_id' => $leader->id]);
    $otherTeam = Team::factory()->create();
    $member = User::factory()->create(['team_id' => $otherTeam->id]);

    $this->actingAs($leader, 'sanctum');

    $response = $this->postJson("api/v1/teams/{$team->id}/members/{$member->id}");

    $response->assertStatus(400);
});

test('It cannot add a member when team is at full capacity', function () {
    /** @var \Tests\TestCase $this */
    $leader = User::factory()->create();
    $leader->assignRole('lider');
    $team = Team::factory()->create(['admin_id' => $leader->id, 'total_members' => 5, 'max_members' => 5]);
    $newMember = User::factory()->create(['team_id' => null]);

    $this->actingAs($leader, 'sanctum');

    $response = $this->postJson("api/v1/teams/{$team->id}/members/{$newMember->id}");

    $response->assertStatus(400);
});

// NOT FOUND (404)

test('It can returns a not found error when the team is not found to add a member', function () {
    /** @var \Tests\TestCase $this */
    $user = User::factory()->create();
    $user->assignRole('lider');
    $member = User::factory()->create(['team_id' => null]);
    $this->actingAs($user, 'sanctum');

    $response = $this->postJson("api/v1/teams/99999/members/{$member->id}");

    $response->assertStatus(404);
});
