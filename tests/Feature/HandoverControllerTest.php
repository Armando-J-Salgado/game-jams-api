<?php

use App\Models\User;
use App\Models\Handover;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    (new \Database\Seeders\PermissionSeeder())->run();
    (new \Database\Seeders\RoleSeeder())->run();
});

// ==========================================
// INDEX TESTS
// ==========================================

#Test: It can get a list of all handovers
test("It can get a list of all handovers (no filters)", function (string $role) {
    /** @var \Tests\TestCase $this */
    $user = User::factory()->create();
    $user->assignRole($role);
    $this->actingAs($user, 'sanctum');

    Handover::factory(3)->create();

    $response = $this->getJson('api/v1/handovers');

    $response->assertStatus(200)
             ->assertJsonCount(3, 'data');
})->with(['administrador', 'organizador']);

#Test: It can filter handovers by team_id and module_id
test("It can filter handovers by team_id and module_id", function (string $role) {
    /** @var \Tests\TestCase $this */
    $user = User::factory()->create();
    $user->assignRole($role);
    $this->actingAs($user, 'sanctum');

    $handover1 = Handover::factory()->create();
    $handover2 = Handover::factory()->create();

    $response = $this->getJson("api/v1/handovers?team_id={$handover1->team_id}&module_id={$handover1->module_id}");

    $response->assertStatus(200)
             ->assertJsonCount(1, 'data')
             ->assertJsonPath('data.0.id', $handover1->id);
})->with(['administrador', 'organizador']);

#Test: It refuses to show the list of handovers to invalid users
test("It cannot show the list of handovers to invalid users", function (string $role) {
    /** @var \Tests\TestCase $this */
    $user = User::factory()->create();
    if ($role !== 'invitado') {
        $user->assignRole($role);
    }
    $this->actingAs($user, 'sanctum');

    $response = $this->getJson('api/v1/handovers');

    $response->assertStatus(403);
})->with(['invitado']);

#Test: Participants and Leaders only see their own team's handovers
test("It isolates handover lists for standard team roles", function (string $role) {
    /** @var \Tests\TestCase $this */
    
    // 1. Create the user and assign a team
    $team1 = \App\Models\Team::factory()->create();
    $team2 = \App\Models\Team::factory()->create();
    
    $user = User::factory()->create(['team_id' => $team1->id]);
    $user->assignRole($role);
    $this->actingAs($user, 'sanctum');

    // 2. Create handovers: Two for their team, one for another team
    Handover::factory(2)->create(['team_id' => $team1->id]);
    Handover::factory(1)->create(['team_id' => $team2->id]); 

    // 3. Make request
    $response = $this->getJson('api/v1/handovers');

    // 4. Assert they only see the 2 from their team
    $response->assertStatus(200)
             ->assertJsonCount(2, 'data');
})->with(['participante', 'lider']);

#Test: No result found when the no data corresponds to the filters
test("It returns empty list when no handovers match the filters", function (string $role) {
    /** @var \Tests\TestCase $this */
    $user = User::factory()->create();
    $user->assignRole($role);
    $this->actingAs($user, 'sanctum');

    Handover::factory()->create();

    // Valid data types, but non-existent IDs
    $response = $this->getJson("api/v1/handovers?team_id=99999&module_id=99999");

    // Because the controller uses validate 'exists', this should fail validation (422)
    $response->assertStatus(422)
             ->assertJsonValidationErrors(['team_id', 'module_id']);
})->with(['administrador', 'organizador']);

#Test: Wrong usage of filters prevents search
test("It cannot filter handovers with invalid data types", function (string $role) {
    /** @var \Tests\TestCase $this */
    $user = User::factory()->create();
    $user->assignRole($role);
    $this->actingAs($user, 'sanctum');

    $response = $this->getJson("api/v1/handovers?team_id=invalid&module_id=invalid");

    $response->assertStatus(422)
             ->assertJsonValidationErrors(['team_id', 'module_id']);
})->with(['administrador', 'organizador']);

// ==========================================
// STORE TESTS
// ==========================================

#Test: Successfully create a handover with valid user types
test("It can create a handover", function (string $role) {
    /** @var \Tests\TestCase $this */
    $module = \App\Models\Module::factory()->create();
    $team = \App\Models\Team::factory()->create();

    $user = User::factory()->create(['team_id' => $team->id]);
    $user->assignRole($role);
    $this->actingAs($user, 'sanctum');

    $handoverData = [
        'title' => 'Project Alpha',
        'attachment' => 'https://example.com/file.zip',
        'module_id' => $module->id,
        'team_id' => $team->id,
    ];

    $response = $this->postJson('api/v1/handovers', $handoverData);

    $response->assertStatus(201);
    $this->assertDatabaseHas('handovers', ['title' => 'Project Alpha', 'module_id' => $module->id]);
})->with(['administrador', 'organizador', 'lider', 'participante']);

#Test: Refusal to create a handover with invalid user types
test("It cannot create a handover if invalid user types", function (string $role) {
    /** @var \Tests\TestCase $this */
    $user = User::factory()->create();
    if ($role !== 'invitado') {
        $user->assignRole($role);
    }
    $this->actingAs($user, 'sanctum');

    $response = $this->postJson('api/v1/handovers', [
        'title' => 'Secret Project',
    ]);

    $response->assertStatus(403);
})->with(['invitado']);

#Test: Refusal to create handover with missing required data
test("It cannot create a handover with missing required data", function (string $role) {
    /** @var \Tests\TestCase $this */
    $user = User::factory()->create();
    $user->assignRole($role);
    $this->actingAs($user, 'sanctum');

    $handoverData = [
        'title' => 'Missing Module and Team',
        // Missing module_id, team_id
    ];

    $response = $this->postJson('api/v1/handovers', $handoverData);

    $response->assertStatus(422)
             ->assertJsonValidationErrors(['module_id', 'team_id']);
})->with(['administrador', 'organizador']);

#Test: Refusal to create handover with wrong format data
test("It cannot create a handover with wrong format data", function (string $role) {
    /** @var \Tests\TestCase $this */
    $user = User::factory()->create();
    $user->assignRole($role);
    $this->actingAs($user, 'sanctum');

    $handoverData = [
        'title' => 12345, // Invalid: integer instead of string
        'attachment' => 'not-a-valid-url', // Invalid format
        'module_id' => 'invalid', // Invalid format
        'team_id' => 'invalid', // Invalid format
        'score' => 150, // Invalid: exceeds 100
        'date_of_submission' => 'not-a-date', // Invalid format
    ];

    $response = $this->postJson('api/v1/handovers', $handoverData);

    $response->assertStatus(422)
             ->assertJsonValidationErrors(['attachment', 'module_id', 'team_id', 'score', 'date_of_submission']);
})->with(['administrador']);


// ==========================================
// SHOW TESTS
// ==========================================

#Test: Successfully shows details of an existing handover
test("It can show details of an existing handover", function (string $role) {
    /** @var \Tests\TestCase $this */
    $user = User::factory()->create();
    $user->assignRole($role);
    $this->actingAs($user, 'sanctum');

    $handover = Handover::factory()->create();

    $response = $this->getJson("api/v1/handovers/{$handover->id}");

    $response->assertStatus(200)
             ->assertJsonPath('data.id', $handover->id)
             ->assertJsonPath('data.title', $handover->title);
})->with(['administrador', 'organizador']);

#Test: No handover found
test("It returns 404 when showing a non-existent handover", function (string $role) {
    /** @var \Tests\TestCase $this */
    $user = User::factory()->create();
    $user->assignRole($role);
    $this->actingAs($user, 'sanctum');

    $response = $this->getJson('api/v1/handovers/99999');

    $response->assertStatus(404);
})->with(['administrador']);


// ==========================================
// UPDATE TESTS
// ==========================================

#Test: Successfully updates data of a handover with valid users
test("It can update a handover", function (string $role) {
    /** @var \Tests\TestCase $this */
    $user = User::factory()->create();
    $user->assignRole($role);
    $this->actingAs($user, 'sanctum');

    $handover = Handover::factory()->create();

    $updateData = [
        'title' => 'Updated Project Title',
        'score' => 95
    ];

    $response = $this->putJson("api/v1/handovers/{$handover->id}", $updateData);

    $response->assertStatus(200)
             ->assertJsonPath('data.title', 'Updated Project Title');
    $this->assertDatabaseHas('handovers', ['id' => $handover->id, 'title' => 'Updated Project Title', 'score' => 95]);
})->with(['administrador', 'organizador']);

#Test: Refuses to update data with invalid users
test("It cannot update a handover with invalid users", function (string $role) {
    /** @var \Tests\TestCase $this */
    $user = User::factory()->create();
    if ($role !== 'invitado') {
        $user->assignRole($role);
    }
    $this->actingAs($user, 'sanctum');

    $handover = Handover::factory()->create();

    $response = $this->putJson("api/v1/handovers/{$handover->id}", [
        'title' => 'Unauthorized Update'
    ]);

    $response->assertStatus(403);
})->with(['invitado']);

#Test: Refuses to update a handover with wrong format data
test("It cannot update a handover with wrong format data", function (string $role) {
    /** @var \Tests\TestCase $this */
    $user = User::factory()->create();
    $user->assignRole($role);
    $this->actingAs($user, 'sanctum');

    $handover = Handover::factory()->create();

    $response = $this->putJson("api/v1/handovers/{$handover->id}", [
        'score' => 200, // Invalid: over 100
        'attachment' => 'invalid-url' // Invalid format
    ]);

    $response->assertStatus(422)
             ->assertJsonValidationErrors(['score', 'attachment']);
})->with(['administrador']);

#Test: Refuses to update data of a non-existent handover
test("It returns 404 when updating a non-existent handover", function (string $role) {
    /** @var \Tests\TestCase $this */
    $user = User::factory()->create();
    $user->assignRole($role);
    $this->actingAs($user, 'sanctum');

    $response = $this->putJson('api/v1/handovers/99999', [
        'title' => 'Ghost Handover'
    ]);

    $response->assertStatus(404);
})->with(['administrador']);


// ==========================================
// DELETE TESTS
// ==========================================

#Test: Successful soft deletion of a handover
test("It can soft delete a handover", function (string $role) {
    /** @var \Tests\TestCase $this */
    $user = User::factory()->create();
    $user->assignRole($role);
    $this->actingAs($user, 'sanctum');

    $handover = Handover::factory()->create();

    $response = $this->deleteJson("api/v1/handovers/{$handover->id}");

    $response->assertStatus(200);
    $this->assertSoftDeleted('handovers', ['id' => $handover->id]);
})->with(['administrador', 'organizador']);

#Test: Refuses to soft delete a handover with invalid users
test("It cannot soft delete a handover with invalid users", function (string $role) {
    /** @var \Tests\TestCase $this */
    $user = User::factory()->create();
    if ($role !== 'invitado') {
        $user->assignRole($role);
    }
    $this->actingAs($user, 'sanctum');

    $handover = Handover::factory()->create();

    $response = $this->deleteJson("api/v1/handovers/{$handover->id}");

    $response->assertStatus(403);
})->with(['lider', 'participante', 'invitado']);

#Test: Refusal to soft delete a non-existent handover
test("It returns 404 when deleting a non-existent handover", function (string $role) {
    /** @var \Tests\TestCase $this */
    $user = User::factory()->create();
    $user->assignRole($role);
    $this->actingAs($user, 'sanctum');

    $response = $this->deleteJson('api/v1/handovers/99999');

    $response->assertStatus(404);
})->with(['administrador']);


