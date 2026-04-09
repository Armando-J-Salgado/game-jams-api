<?php

use App\Models\Module;
use App\Models\Team;
use App\Models\Competition;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    (new PermissionSeeder())->run();
    (new RoleSeeder())->run();

    test()->user = User::factory()->create();
    test()->competition = Competition::factory()->create([
        'start_date'=>'2026-10-01',
        'end_date'=>'2026-10-12',
        'admin_id'=>test()->user->id
    ]);
    test()->competition2 = Competition::factory()->create();
    test()->module2 = Module::factory()->create([
        'due_date'=>'2026-12-12',
        'competition_id'=>test()->competition->id
    ]);
    test()->module = Module::factory()->create([
        'competition_id'=>test()->competition2->id,
        'title'=>'ESEN TEST EXAMPLE',
        'due_date'=>'2026-03-12',
    ]);
});

afterEach(function () {
    test()->module2->forceDelete();
    test()->module->forceDelete();
    test()->competition->forceDelete();
    test()->competition2->forceDelete();
    test()->user->forceDelete();
});

//Test #27 it cannot store if not authenticated
it ("can't store a module if not authenticated", function () {
    $this->postJson('api/v1/modules', [
        'title'=> 'example',
        'description' => 'example',
        'due_date'=>'2026-10-02',
        'competition_id'=>test()->competition->id,
    ])->assertStatus(401);
});

//Test #28 it cannot store modules if not authorized
it ("can't store module if not authorized", function (string $role) {
    $user = User::factory()->create();
    $user->assignRole($role);
    $this->actingAs($user);

    $competition = Competition::factory()->create([
        'start_date'=>'2026-10-01',
        'end_date'=>'2026-10-12',
        'admin_id'=>$user->id
    ]);

    $this->postJson('api/v1/modules', [
        'title'=> 'example',
        'description' => 'example',
        'due_date'=>'2026-10-02',
        'competition_id'=>$competition->id,
    ])->assertStatus(403); 
})->with(['participante', 'lider']);

//Test #29 it cannot store modules in a third-party competition
it ("can't store modules in a third-party competition", function (string $role) {
    $user = User::factory()->create();
    $user->assignRole($role);
    $this->actingAs($user);

    $this->postJson('api/v1/modules', [
        'title'=> 'example',
        'description' => 'example',
        'due_date'=>'2026-10-02',
        'competition_id'=>test()->competition->id,
    ])->assertStatus(403);
 
})->with(['administrador', 'organizador']);

//Test #30 it cannot store a module with invalid date
it ("can't store a module with invalid date", function (string $role) {
    $user = User::factory()->create();
    $user->assignRole($role);
    $this->actingAs($user);

    $competition = Competition::factory()->create([
        'start_date'=>'2026-10-01',
        'end_date'=>'2026-10-12',
        'admin_id'=>$user->id
    ]);

    $this->postJson('api/v1/modules', [
        'title'=> 'example',
        'description' => 'example',
        'due_date'=>'2026-11-01',
        'competition_id'=>$competition->id,
    ])->assertJsonValidationErrors(['due_date']);
    
})->with(['administrador', 'organizador']);

//Test #31 it cannot store a module with invalid competition id
it ("can't store a module with invalid competition id", function (string $role) {
    $user = User::factory()->create();
    $user->assignRole($role);
    $this->actingAs($user);

    $this->postJson('api/v1/modules', [
        'title'=> 'example',
        'description' => 'example',
        'due_date'=>'2026-10-05',
        'competition_id'=>100,
    ])->assertJsonValidationErrors(['competition_id']);
    
})->with(['administrador', 'organizador']);

//Test #32 it can store a module
it ("can store a module", function (string $role) {
    $user = User::factory()->create();
    $user->assignRole($role);
    $this->actingAs($user);

    $competition = Competition::factory()->create([
        'start_date'=>'2026-10-01',
        'end_date'=>'2026-10-12',
        'admin_id'=>$user->id
    ]);

    $this->postJson('api/v1/modules', [
        'title'=> 'example',
        'description' => 'example',
        'due_date'=>'2026-10-05',
        'competition_id'=> $competition->id,
    ])->assertStatus(201)->assertJsonStructure([
        "message",
        "module"=> [
            "title",
            "description",
            "attachments",
            "due_date",
            "competition" => [
                "id",
                "name"
            ],
            "created_at",
            "updated_at"
        ]
    ]);
})->with(['administrador', 'organizador']);

//Test #33 it can store module and its necessary handovers
it ("can store a module and its necessary handovers", function (string $role) {
    $user = User::factory()->create();
    $user->assignRole($role);
    $this->actingAs($user);

    $team = Team::factory()->create();

    $competition = Competition::factory()->create([
        'start_date'=>'2026-10-01',
        'end_date'=>'2026-10-12',
        'admin_id'=>$user->id
    ]);

    $competition->teams()->attach($team->id);

    $this->postJson('api/v1/modules', [
        'title'=> 'example',
        'description' => 'example',
        'due_date'=>'2026-10-05',
        'competition_id'=> $competition->id,
    ]);

    $team->refresh();
    expect($team->handovers->count())->toBe(1);
})->with(['administrador', 'organizador']);

//Test #34 it cannot index without authentication
it ("can't index modules if not authenticated", function () {
    $this->getJson('api/v1/modules')->assertStatus(401);
});

//Test #35 it can filter by title
it ("can filter modules by title", function (string $role) {
    $user = User::factory()->create();
    $user->assignRole($role);
    $this->actingAs($user);

    $this->getJson('api/v1/modules?title=ESEN TEST EXAMPLE')
        ->assertJsonCount(1);

})->with(['administrador', 'organizador', 'lider', 'participante']);

//Test #36 it can filter by due date
it ("can filter modules by due date", function (string $role) {
    $user = User::factory()->create();
    $user->assignRole($role);
    $this->actingAs($user);

    $this->getJson('api/v1/modules?due_date=2026-04-12')
        ->assertJsonCount(1);

})->with(['administrador', 'organizador', 'participante', 'lider']);

//Test #37 it can filter by competition id
it ("can filter modules by competition id", function (string $role) {
    $user = User::factory()->create();
    $user->assignRole($role);
    $this->actingAs($user);

    $this->getJson('api/v1/modules?competition_id=1')
        ->assertJsonCount(1);

})->with(['administrador', 'organizador', 'participante', 'lider']);

//Test #38 it can filter trashed modules
it ("can filter trashed modules", function (string $role) {
    $user = User::factory()->create();
    $user->assignRole($role);
    $this->actingAs($user);

    test()->module->delete();

    $this->getJson('api/v1/modules?is_trashed=1')
        ->assertJsonCount(1);

})->with(['administrador', 'organizador', 'participante', 'lider']);

//Test #39 it can index modules with pagination
it ("can index modules with pagination", function(string $role) {
    $user = User::factory()->create();
    $user->assignRole($role);
    $this->actingAs($user);

    $this->getJson('api/v1/modules?per_page=1&page=2')
        ->assertJsonCount(1)
        ->assertJsonFragments([
            'data'=> [
            'title'=>test()->module->title,
            ]
        ]);

})->with(['administrador', 'organizador', 'lider', 'participante']);

//Test #40 can not show details of an invalid module id
it("can't show details of an invalid module id", function() {
    $user = User::factory()->create();
    $this->actingAs($user);
    $this->getJson('/api/v1/modules/100')->assertStatus(404);
});

//Test #41 can not show details of a module if not authenticated
it("can't show details of a module when not authenticated", function() {
    $this->getJson('api/v1/modules/'.test()->module->id)->assertStatus(401);
});

//Test #42 can show details of a module
it('can show details of a module', function(string $role) {
    $user = User::factory()->create();
    $user->assignRole($role);
    $this->actingAs($user);

    $this->getJson('api/v1/modules/'.test()->module->id)
        ->assertJsonFragment(
            [[
                "title"=>test()->module->title,
                "description"=>test()->module->description,
                "attachments"=>test()->module->attachments,
                "due_date"=>test()->module->due_date,
                "competition"=> [
                    "id" => test()->module->competition->id,
                    "name" => test()->module->competition->name
                ],
                "created_at" => test()->module->created_at,
                "updated_at" => test()->module->updated_at
            ]]);
})->with(['administrador', 'organizador', 'lider', 'participante']);

//Test #43 it can't update if no authenticated
it("can't update a module if not authenticated", function () {
    $this->putJson('api/v1/modules/'.test()->module->id, [
        'title'=>'Updated title',
        'description'=>'Updated description'
    ])->assertStatus(401);
});

//Test #44 it can't update an invalid module id
it("can't update with an invalid module id", function (string $role) {
    $user = User::factory()->create();
    $user->assignRole($role);
    $this->actingAs($user);
    
    $this->putJson('api/v1/modules/9000', [
        'title'=>'Updated title',
        'description'=>'Updated description'        
    ])->assertStatus(404);
})->with(['administrador', 'organizador']);

//Test #45 it can't update a third party's competition
it("can't update a module of a third party's competition", function (string $role) {
    $user = User::factory()->create();
    $user->assignRole($role);
    $this->actingAs($user);
    
    $this->putJson('api/v1/modules/'.test()->module->id, [
        'title'=>'Updated title',
        'description'=>'Updated description'        
    ])->assertStatus(403);
})->with(['administrador', 'organizador']);

//Test #46 it can't modify a competition without authorization
it("can't modify a module without authorization", function(string $role) {
    $user = User::factory()->create();
    $user->assignRole($role);
    $this->actingAs($user);

    $competition = Competition::factory()->create([
        'start_date'=>'2026-10-01',
        'end_date'=>'2026-10-12',
        'admin_id'=>$user->id
    ]);

    $testModule = Module::factory()->create([
        'competition_id'=> $competition->id,
    ]);

    $this->putJson('api/v1/modules/'.$testModule->id, [
        'title'=>'Updated title',
        'description'=>'Updated description'
    ])->assertStatus(403);

})->with(['lider', 'participante']);

//Test #47 it can't update module with invalid date
it("can't update a module with an invalid date", function(string $role) {
    $user = User::factory()->create();
    $user->assignRole($role);
    $this->actingAs($user);

    $competition = Competition::factory()->create([
        'start_date'=>'2026-10-01',
        'end_date'=>'2026-10-12',
        'admin_id'=>$user->id
    ]);

    $testModule = Module::factory()->create([
        'competition_id'=> $competition->id,
    ]);

    $this->putJson('api/v1/modules/'.$testModule->id, [
        'title'=>'Updated title',
        'description'=>'Updated description',
        'due_date'=>'2027-10-11'
    ])->assertJsonValidationErrors(['due_date']);

})->with(['administrador', 'organizador']);

//Test #48 it can update a module
it("can update a module", function(string $role) {
    $user = User::factory()->create();
    $user->assignRole($role);
    $this->actingAs($user);

    $competition = Competition::factory()->create([
        'start_date'=>'2026-10-01',
        'end_date'=>'2026-10-12',
        'admin_id'=>$user->id
    ]);

    $testModule = Module::factory()->create([
        'competition_id'=> $competition->id,
    ]);

    $this->putJson('api/v1/modules/'.$testModule->id, [
        'title'=>'Updated title',
        'description'=>'Updated description',
        'due_date'=>'2026-10-11'
    ])->assertJsonFragments([
        [
        'title'=>'Updated title',
        'description'=>'Updated description',
        'due_date'=>'2026-10-11'
        ]        
    ]);

})->with(['administrador', 'organizador']);

//Test #49 it can't delete if not authenticated
it("can't delete a module if not authenticated", function() {
    $this->deleteJson('api/v1/modules/'.test()->module->id)->assertStatus(401);
});

//Test #50 it can't delete a module with an invalid id
it("can't delete a module with an invalid id", function(string $role) {
    $user = User::factory()->create();
    $user->assignRole($role);
    $this->actingAs($user);

    $this->deleteJson('api/v1/modules/4000')
        ->assertStatus(404);
})->with(['administrador']);

//Test #51 it can't delete if not authorized
it("can't delete a module if no authorized", function(string $role) {
    $user = User::factory()->create();
    $user->assignRole($role);
    $this->actingAs($user);

    $competition = Competition::factory()->create([
        'start_date'=>'2026-10-01',
        'end_date'=>'2026-10-12',
        'admin_id'=>$user->id
    ]);

    $testModule = Module::factory()->create([
        'competition_id'=> $competition->id,
    ]);

    $this->deleteJson('api/v1/modules/'.$testModule->id)
        ->assertStatus(403);
})->with(['organizador', 'lider', 'participante']);

//Test #52 it can delete a module
it("can delete a module", function(string $role) {
    $user = User::factory()->create();
    $user->assignRole($role);
    $this->actingAs($user);

    $competition = Competition::factory()->create([
        'start_date'=>'2026-10-01',
        'end_date'=>'2026-10-12',
        'admin_id'=>$user->id
    ]);

    $testModule = Module::factory()->create([
        'competition_id'=> $competition->id,
    ]);

    $this->deleteJson('api/v1/modules/'.$testModule->id)
        ->assertStatus(200);
    
    $testModule->refresh();
    expect($testModule->trashed())->toBeTrue();
})->with(['administrador']);

//Test #60: It cannot restore a non-deleted module
it('cannot restore a non-deleted module', function (string $role) {

    $user = User::factory()->create();
    $user->assignRole($role);
    $this->actingAs($user);

    $competition = Competition::factory()->create([
        'start_date'=>'2026-10-01',
        'end_date'=>'2026-10-12',
        'admin_id'=>$user->id
    ]);
    
    $testModule = Module::factory()->create([
        'competition_id'=> $competition->id,
    ]);

    $this->postJson("api/v1/modules/{$testModule->id}/restore")
        ->assertStatus(400);

})->with(['administrador']);

//Test #61: It cannot restore a deleted module without authorization
it('cannot restore a deleted module withouth authorization', function (string $role) {

    $user = User::factory()->create();
    $user->assignRole($role);
    $this->actingAs($user);

    $competition = Competition::factory()->create([
        'start_date'=>'2026-10-01',
        'end_date'=>'2026-10-12',
        'admin_id'=>$user->id
    ]);
    
    $testModule = Module::factory()->create([
        'competition_id'=> $competition->id,
    ]);

    $testModule->delete();

    $this->postJson("api/v1/modules/{$testModule->id}/restore")
        ->assertStatus(403);

})->with(['organizador', 'lider', 'participante']);

//Test #62: It cannot restore a deleted module from a third-party competition
it('cannot restore a deleted module from a third-party competition', function (string $role) {

    $user = User::factory()->create();
    $user->assignRole($role);
    $this->actingAs($user);

    $thirdParty = User::factory()->create();

    $competition = Competition::factory()->create([
        'start_date'=>'2026-10-01',
        'end_date'=>'2026-10-12',
        'admin_id'=>$thirdParty->id
    ]);
    
    $testModule = Module::factory()->create([
        'competition_id'=> $competition->id,
    ]);

    $testModule->delete();

    $this->postJson("api/v1/modules/{$testModule->id}/restore")
        ->assertStatus(403);

})->with(['administrador']);

//Test #63: It cannot restore a deleted module without authentication
it('cannot restore a deleted module without authentication', function () {

    $user = User::factory()->create();
    $competition = Competition::factory()->create([
        'start_date'=>'2026-10-01',
        'end_date'=>'2026-10-12',
        'admin_id'=>$user->id
    ]);
    
    $testModule = Module::factory()->create([
        'competition_id'=> $competition->id,
    ]);

    $testModule->delete();

    $this->postJson("api/v1/modules/{$testModule->id}/restore")
        ->assertUnauthorized();

});

//Test #64: It cannot restore a deleted module with invalid ID
it('cannot restore a deleted module with invalid ID', function (string $role) {

    $user = User::factory()->create();
    $user->assignRole($role);
    $this->actingAs($user);
    $competition = Competition::factory()->create([
        'start_date'=>'2026-10-01',
        'end_date'=>'2026-10-12',
        'admin_id'=>$user->id
    ]);
    
    $testModule = Module::factory()->create([
        'competition_id'=> $competition->id,
    ]);

    $testModule->delete();

    $this->postJson("api/v1/modules/AAA/restore")
        ->assertStatus(404);

})->with(['administrador']);

//Test #65: It can restore a deleted module
it('can restore a deleted module', function (string $role) {

    $user = User::factory()->create();
    $user->assignRole($role);
    $this->actingAs($user);
    $competition = Competition::factory()->create([
        'start_date'=>'2026-10-01',
        'end_date'=>'2026-10-12',
        'admin_id'=>$user->id
    ]);
    
    $testModule = Module::factory()->create([
        'competition_id'=> $competition->id,
    ]);

    $testModule->delete();

    $this->postJson("api/v1/modules/{$testModule->id}/restore")
        ->assertStatus(200);
    
    $testModule->refresh();
    expect($testModule->trashed())->toBeFalse();

})->with(['administrador']);