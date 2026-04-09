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

test('A leader can remove a member from their team', function () {
    /** @var \Tests\TestCase $this */
    $leader = User::factory()->create();
    $leader->assignRole('lider');
    $team = Team::factory()->create(['admin_id' => $leader->id, 'total_members' => 2]);
    $member = User::factory()->create(['team_id' => $team->id]);

    $this->actingAs($leader, 'sanctum');

    $response = $this->deleteJson("api/v1/teams/{$team->id}/members/{$member->id}");

    $response->assertStatus(200)
        ->assertJsonPath('message', 'Member removed successfully from the team.')
        ->assertJsonPath('data.total_members', 1);

    $this->assertDatabaseHas('users', [
        'id'      => $member->id,
        'team_id' => null,
    ]);
});

test('Removing a member decrements the team total_members', function () {
    /** @var \Tests\TestCase $this */
    $leader = User::factory()->create();
    $leader->assignRole('lider');
    $team = Team::factory()->create(['admin_id' => $leader->id, 'total_members' => 2]);
    $member = User::factory()->create(['team_id' => $team->id]);

    $this->actingAs($leader, 'sanctum');
    $this->deleteJson("api/v1/teams/{$team->id}/members/{$member->id}");

    $this->assertDatabaseHas('teams', ['id' => $team->id, 'total_members' => 1]);
});

// AUTHORIZATION (403)

test('A non-leader cannot remove members from another team', function () {
    /** @var \Tests\TestCase $this */
    $intruder = User::factory()->create();
    $intruder->assignRole('lider');
    $team = Team::factory()->create(); // different admin
    $member = User::factory()->create(['team_id' => $team->id]);

    $this->actingAs($intruder, 'sanctum');

    $response = $this->deleteJson("api/v1/teams/{$team->id}/members/{$member->id}");

    $response->assertStatus(403);
});

// BUSINESS RULES (400)

test('A leader cannot remove themselves from the team', function () {
    /** @var \Tests\TestCase $this */
    $leader = User::factory()->create();
    $leader->assignRole('lider');
    $team = Team::factory()->create(['admin_id' => $leader->id]);

    $this->actingAs($leader, 'sanctum');

    $response = $this->deleteJson("api/v1/teams/{$team->id}/members/{$leader->id}");

    $response->assertStatus(400);
});

test('Cannot remove a user who is not a member of the team', function () {
    /** @var \Tests\TestCase $this */
    $leader = User::factory()->create();
    $leader->assignRole('lider');
    $team = Team::factory()->create(['admin_id' => $leader->id]);
    $outsider = User::factory()->create(['team_id' => null]);

    $this->actingAs($leader, 'sanctum');

    $response = $this->deleteJson("api/v1/teams/{$team->id}/members/{$outsider->id}");

    $response->assertStatus(400);
});

// NOT FOUND (404)

test('Returns 404 when team is not found on remove member', function () {
    /** @var \Tests\TestCase $this */
    $user = User::factory()->create();
    $user->assignRole('lider');
    $member = User::factory()->create(['team_id' => null]);
    $this->actingAs($user, 'sanctum');

    $response = $this->deleteJson("api/v1/teams/99999/members/{$member->id}");

    $response->assertStatus(404);
});
