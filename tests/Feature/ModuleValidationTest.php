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
});

//Test # it cannot store if not authenticated
it ("can't store a module if not authenticated", function () {
    $this->postJson('api/v1/modules', [
        'title'=> 'example',
        'description' => 'example',
        'due_date'=>'2026-10-02',
        'competition_id'=>test()->competition->id,
    ])->assertStatus(401);
});

//Test # it cannot store modules if not authorized
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

//Test # it cannot store modules in a third-party competition
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

//Test # it cannot store a module with invalid dates
it ("can't store a module with invalid dates", function (string $role) {
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

//Test # it cannot store a module with invalid competition id
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

//Test # it can store a module
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

//Test # it can store module and its neccessary handovers
it ("can store a module and its neccessary handovers", function (string $role) {
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

//Test # it cannot index without authorization
it ("can't index modules if not authenticated", function () {
    $this->getJson('api/v1/modules')->assertStatus(401);
});

//Test # it can filter by title
it ("can filter modules by title", function (string $role) {
    $user = User::factory()->create();
    $user->assignRole($role);
    $this->actingAs($user);

    $this->getJson('api/v1/modules?title=ESEN TEST EXAMPLE')
        ->assertJsonCount(1);

})->with(['administrador', 'organizador', 'lider', 'participante']);

//Test # it can filter by due date
it ("can filter modules by due date", function (string $role) {
    $user = User::factory()->create();
    $user->assignRole($role);
    $this->actingAs($user);

    $this->getJson('api/v1/modules?due_date=2026-04-12')
        ->assertJsonCount(1);

})->with(['administrador', 'organizador', 'participante', 'lider']);

//Test # it can filter by competition id
it ("can filter modules by competition id", function (string $role) {
    $user = User::factory()->create();
    $user->assignRole($role);
    $this->actingAs($user);

    $this->getJson('api/v1/modules?competition_id=1')
        ->assertJsonCount(1);

})->with(['administrador', 'organizador', 'participante', 'lider']);

//Test # it can filter trashed modules
it ("can filter trashed modules", function (string $role) {
    $user = User::factory()->create();
    $user->assignRole($role);
    $this->actingAs($user);

    test()->module->delete();

    $this->getJson('api/v1/modules?is_trashed=1')
        ->assertJsonCount(1);

})->with(['administrador', 'organizador', 'participante', 'lider']);

//Test # it can index modules with pagination
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

//Test # can not show details of an invalid module id
it("can't show details of an invalid module id", function() {
    $user = User::factory()->create();
    $this->actingAs($user);
    $this->getJson('/api/v1/modules/100')->assertStatus(404);
});

//Test # can not show details of a module if not authenticated
it("can't show details of a module when not authenticated", function() {
    $this->getJson('api/v1/modules/'.test()->module->id)->assertStatus(401);
});

//Test # can show details of a module
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

//todo: update validations y delete