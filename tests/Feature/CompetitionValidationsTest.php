<?php

use App\Models\Category;
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
    test()->category = Category::factory()->create();
    test()->competition = Competition::create([
        "name" => "Test Competition",
        "description" => "Test description",
        "prize_information" => "Test prize information",
        "tools_information" => "Test tools information",
        "max_teams" => 10,
        "start_date" => "2026-07-04",
        "end_date" => "2026-07-06",
        "admin_id" => test()->user->id,
        "category_id" => test()->category->id,
    ]);
    test()->competition2 = Competition::create([
        "name" => "New Competition",
        "description" => "New description",
        "prize_information" => "New prize information",
        "tools_information" => "New tools information",
        "max_teams" => 20,
        "start_date" => "2026-06-04",
        "end_date" => "2026-06-06",
        "admin_id" => test()->user->id,
        "category_id" => test()->category->id,
    ]);
    test()->competition3 = Competition::create([
        "name" => "New Competition",
        "description" => "New description",
        "prize_information" => "New prize information",
        "tools_information" => "New tools information",
        "max_teams" => 20,
        "start_date" => "2026-06-04",
        "end_date" => "2026-06-06",
        "admin_id" => test()->user->id,
        "category_id" => test()->category->id,
        "is_finished"=>true,
    ]);
});

afterEach(function() {
    test()->competition->forceDelete();
    test()->competition2->forceDelete();
    test()->competition3->forceDelete();
    test()->category->forceDelete();
});

//Test #1 Comprueba filtrado por nombre
it('can filter competitions by name', function (string $role) {
    $competition = test()->competition;
    $user = User::factory()->create();
    $user->assignRole($role);
    $this->actingAs($user);
    $this->getJson('api/v1/competitions?name=Test')
        ->assertJsonFragments([
            'data'=> [
                'id'=>$competition->id,
                'name'=>$competition->name,
                'description'=>$competition->description,
                'prize'=>$competition->prize_information,
                'tools'=>$competition->tools_information,
                'total_number_of_teams'=>0,
                'maximum_number_of_teams'=>$competition->max_teams,
                "registration_start_date" => $competition->start_date,
                "registration_closing_date" => $competition->end_date,
                "competition_is_finished"=>false,
                "admin"=>$competition->user->name,
                "category"=>$competition->category->name,
                "teams"=>$competition->teams,
                "modules"=>$competition->modules
                ]
            ]);
})->with(['administrador', 'organizador', 'lider', 'participante']);

//Test #2 filtro por fechas
it('can filter competitions by date', function (string $role) {
    $competition = test()->competition2;
    $user = User::factory()->create();
    $user->assignRole($role);
    $this->actingAs($user);
    $this->getJson('api/v1/competitions?start_date=2026-06-01&end_date=2026-07-01')
        ->assertJsonFragments([
            'data'=> [
                'id'=>$competition->id,
                'name'=>$competition->name,
                'description'=>$competition->description,
                'prize'=>$competition->prize_information,
                'tools'=>$competition->tools_information,
                'total_number_of_teams'=>0,
                'maximum_number_of_teams'=>$competition->max_teams,
                "registration_start_date" => $competition->start_date,
                "registration_closing_date" => $competition->end_date,
                "competition_is_finished"=>false,
                "admin"=>$competition->user->name,
                "category"=>$competition->category->name,
                "teams"=>$competition->teams,
                "modules"=>$competition->modules
                ]
            ]);
})->with(['administrador', 'organizador', 'lider', 'participante']);

//Test #3 filtro por finalización
it('can filter finished competitions', function (string $role) {
    $competition = test()->competition3;
    $user = User::factory()->create();
    $user->assignRole($role);
    $this->actingAs($user);
    $this->getJson('api/v1/competitions?is_finished=1')
        ->assertJsonFragments([
            'data'=> [
                'id'=>$competition->id,
                'name'=>$competition->name,
                'description'=>$competition->description,
                'prize'=>$competition->prize_information,
                'tools'=>$competition->tools_information,
                'total_number_of_teams'=>0,
                'maximum_number_of_teams'=>$competition->max_teams,
                "registration_start_date" => $competition->start_date,
                "registration_closing_date" => $competition->end_date,
                "competition_is_finished"=> $competition->is_finished,
                "admin"=>$competition->user->name,
                "category"=>$competition->category->name,
                "teams"=>$competition->teams,
                "modules"=>$competition->modules
                ]
            ]);
})->with(['administrador', 'organizador', 'lider', 'participante']);

//Test #4 filtro por eliminación
it('can filter deleted competitions', function (string $role) {
    $competition = test()->competition3;
    $competition->delete();
    $user = User::factory()->create();
    $user->assignRole($role);
    $this->actingAs($user);
    $this->getJson('api/v1/competitions?is_trashed=1')
        ->assertJsonFragments([
            'data'=> [
                'id'=>$competition->id,
                'name'=>$competition->name,
                'description'=>$competition->description,
                'prize'=>$competition->prize_information,
                'tools'=>$competition->tools_information,
                'total_number_of_teams'=>0,
                'maximum_number_of_teams'=>$competition->max_teams,
                "registration_start_date" => $competition->start_date,
                "registration_closing_date" => $competition->end_date,
                "competition_is_finished"=> $competition->is_finished,
                "admin"=>$competition->user->name,
                "category"=>$competition->category->name,
                "teams"=>$competition->teams,
                "modules"=>$competition->modules
                ]
            ]);
})->with(['administrador', 'organizador', 'lider', 'participante']);

//Test #5 Index con paginación
it('can filter competitions with pagination', function (string $role) {
    $competition1 = test()->competition;
    $competition2 = test()->competition2;
    $user = User::factory()->create();
    $user->assignRole($role);
    $this->actingAs($user);
    $this->getJson('api/v1/competitions?page=1&per_page=2')
        ->assertJsonFragments([
            'data'=> [
                    'id'=>$competition1->id,
                    'name'=>$competition1->name,
                    'description'=>$competition1->description,
                    'prize'=>$competition1->prize_information,
                    'tools'=>$competition1->tools_information,
                    'total_number_of_teams'=>0,
                    'maximum_number_of_teams'=>$competition1->max_teams,
                    "registration_start_date" => $competition1->start_date,
                    "registration_closing_date" => $competition1->end_date,
                    "competition_is_finished"=> false,
                    "admin"=>$competition1->user->name,
                    "category"=>$competition1->category->name,
                    "teams"=>$competition1->teams,
                    "modules"=>$competition1->modules
                ],
                [
                    'id'=>$competition2->id,
                    'name'=>$competition2->name,
                    'description'=>$competition2->description,
                    'prize'=>$competition2->prize_information,
                    'tools'=>$competition2->tools_information,
                    'total_number_of_teams'=>0,
                    'maximum_number_of_teams'=>$competition2->max_teams,
                    "registration_start_date" => $competition2->start_date,
                    "registration_closing_date" => $competition2->end_date,
                    "competition_is_finished"=>false,
                    "admin"=>$competition2->user->name,
                    "category"=>$competition2->category->name,
                    "teams"=>$competition2->teams,
                    "modules"=>$competition2->modules
                ]
            ]);
})->with(['administrador', 'organizador', 'lider', 'participante']);

//Test #6 Authorization error
it ("can't index without authenthication", function() {
    $this->getJson('api/v1/competitions')
        ->assertStatus(401);
});

//Test #7 Id not found
it ("can't show competition detail with a wrong id", function () {
    $user = User::factory()->create();
    $this->actingAs($user);
    $this->getJson('api/v1/competitions/1000')
        ->assertStatus(404);
});

//Test #8 It can show competitions detail
it('can show competition details', function (string $role) {
    $competition = test()->competition;
    $user = User::factory()->create();
    $user->assignRole($role);
    $this->actingAs($user);
    $this->getJson('api/v1/competitions/'.$competition->id)
        ->assertJsonFragments([
            'data'=> [
                'id'=>$competition->id,
                'name'=>$competition->name,
                'description'=>$competition->description,
                'prize'=>$competition->prize_information,
                'tools'=>$competition->tools_information,
                'total_number_of_teams'=>0,
                'maximum_number_of_teams'=>$competition->max_teams,
                "registration_start_date" => $competition->start_date,
                "registration_closing_date" => $competition->end_date,
                "competition_is_finished"=>false,
                "admin"=>$competition->user->name,
                "category"=>$competition->category->name,
                "teams"=>$competition->teams,
                "modules"=>$competition->modules
                ]
            ]);
})->with(['administrador', 'organizador', 'lider', 'participante']);

//Test #9 it can't store a competition with invalid dates
it("can't store a competition with invalid dates", function (string $role) {
    $user = User::factory()->create();
    $user->assignRole($role);
    $this->actingAs($user);

    $this->postJson('api/v1/competitions',
        [
            "name"=> "ESEN 2026 GAME JAM FRIGHTS",
            "description"=> "Come along and release your worst nightmares in this new edition. Create a videogame focused on a Phobia and spread fun and scares around the campus",
            "prize_information"=> "Earn a recognition as a Software Developer with your own official diploma. First place takes home the money from the ESEN 2026 Arcade!",
            "tools_information"=> "Prepare to use itch.io and GODOT game engine",
            "max_teams"=> 10,
            "start_date"=>"2026-07-10",
            "end_date"=> "2026-07-06",
            "category_id"=>1,
            "admin_id"=>$user->id
    ])->assertJsonValidationErrors(['end_date']);
})->with(['administrador', 'organizador']);

//Test #10 can't store competitions with invalid category
it("can't store competitions with invalid category", function (string $role) {
    $user = User::factory()->create();
    $user->assignRole($role);
    $this->actingAs($user);
    $this->postJson('api/v1/competitions',
        [
            "name"=> "ESEN 2026 GAME JAM FRIGHTS",
            "description"=> "Come along and release your worst nightmares in this new edition. Create a videogame focused on a Phobia and spread fun and scares around the campus",
            "prize_information"=> "Earn a recognition as a Software Developer with your own official diploma. First place takes home the money from the ESEN 2026 Arcade!",
            "tools_information"=> "Prepare to use itch.io and GODOT game engine",
            "max_teams"=> 10,
            "start_date"=>"2026-07-01",
            "end_date"=> "2026-07-06",
            "category_id"=>100,
            "admin_id"=>$user->id
    ])->assertJsonValidationErrors(['category_id']);
})->with(['administrador', 'organizador']);

//Test #11 can't store competition without authorization
it("can't store competitions without authorization", function(string $role) {
    $user = User::factory()->create();
    $user->assignRole($role);
    $this->actingAs($user);

    $this->postJson('api/v1/competitions', 
        [
            "name"=> "ESEN 2026 GAME JAM FRIGHTS",
            "description"=> "Come along and release your worst nightmares in this new edition. Create a videogame focused on a Phobia and spread fun and scares around the campus",
            "prize_information"=> "Earn a recognition as a Software Developer with your own official diploma. First place takes home the money from the ESEN 2026 Arcade!",
            "tools_information"=> "Prepare to use itch.io and GODOT game engine",
            "max_teams"=> 10,
            "start_date"=>"2026-07-01",
            "end_date"=> "2026-07-06",
            "category_id"=>test()->category->id,
            "admin_id"=>$user->id        
        ])->assertStatus(403);
})->with(['lider', 'participante']);

//Test #12 can store competition
it("can store competitions", function(string $role) {
    $user = User::factory()->create();
    $user->assignRole($role);
    $this->actingAs($user);

    $this->postJson('api/v1/competitions', [
            "name"=> "ESEN 2026 GAME JAM FRIGHTS",
            "description"=> "Come along and release your worst nightmares in this new edition. Create a videogame focused on a Phobia and spread fun and scares around the campus",
            "prize_information"=> "Earn a recognition as a Software Developer with your own official diploma. First place takes home the money from the ESEN 2026 Arcade!",
            "tools_information"=> "Prepare to use itch.io and GODOT game engine",
            "max_teams"=> 10,
            "start_date"=>"2026-07-01",
            "end_date"=> "2026-07-06",
            "category_id"=>test()->category->id,
            "admin_id"=>$user->id
    ])->assertStatus(201)->assertJsonStructure([
        "message",
        "competition"=> [
            "id",
            "name",
            "description",
            "prize",
            "tools",
            "maximum_number_of_teams",
            "total_number_of_teams",
            "registration_start_date",
            "registration_closing_date",
            "competition_is_finished",
            "category",
            "admin",
            "teams",
            "modules"
        ]
    ]);
})->with(['administrador', 'organizador']);

//Test #13 can't exceed the maximum amount of members
it ("can't exceed the maximum amount of members", function (string $role) {
    $user = User::factory()->create();
    $user->assignRole($role);
    $this->actingAs($user);

    $testCompetition = Competition::create([
        "name" => "Test Competition",
        "description" => "Test description",
        "prize_information" => "Test prize information",
        "tools_information" => "Test tools information",
        "max_teams" => 10,
        "start_date" => "2026-07-04",
        "end_date" => "2026-07-06",
        "admin_id" => $user->id,
        "category_id" => test()->category->id,
    ]);

    $this->putJson('api/v1/competitions/'.$testCompetition->id, [
        'total_teams'=> 20
    ])->assertJsonValidationErrors(['total_teams']);

})->with(['administrador', 'organizador']);

//Test #14 can't update with invalid start date
it ("can't update with invalid start date", function (string $role) {
    $user = User::factory()->create();
    $user->assignRole($role);
    $this->actingAs($user);

    $testCompetition = Competition::create([
        "name" => "Test Competition",
        "description" => "Test description",
        "prize_information" => "Test prize information",
        "tools_information" => "Test tools information",
        "max_teams" => 10,
        "start_date" => "2026-07-04",
        "end_date" => "2026-07-06",
        "admin_id" => $user->id,
        "category_id" => test()->category->id,
    ]);

    $this->putJson('api/v1/competitions/'. $testCompetition->id, [
        'start_date'=> '2027-01-01'
    ])->assertJsonValidationErrors(['start_date']);
    
})->with(['administrador', 'organizador']);

//Test #15 can't update with invalid end date
it ("can't update with invalid end date", function (string $role) {
    $user = User::factory()->create();
    $user->assignRole($role);
    $this->actingAs($user);

    $testCompetition = Competition::create([
        "name" => "Test Competition",
        "description" => "Test description",
        "prize_information" => "Test prize information",
        "tools_information" => "Test tools information",
        "max_teams" => 10,
        "start_date" => "2026-07-04",
        "end_date" => "2026-07-06",
        "admin_id" => $user->id,
        "category_id" => test()->category->id,
    ]);

    $this->putJson('api/v1/competitions/'. $testCompetition->id, [
        'end_date'=> '2025-01-01'
    ])->assertJsonValidationErrors(['end_date']);
    
})->with(['administrador', 'organizador']);

//Test #16 can't update with invalid category
it ("can't update with invalid category", function (string $role) {
    $user = User::factory()->create();
    $user->assignRole($role);
    $this->actingAs($user);

    $testCompetition = Competition::create([
        "name" => "Test Competition",
        "description" => "Test description",
        "prize_information" => "Test prize information",
        "tools_information" => "Test tools information",
        "max_teams" => 10,
        "start_date" => "2026-07-04",
        "end_date" => "2026-07-06",
        "admin_id" => $user->id,
        "category_id" => test()->category->id,
    ]);

    $this->putJson('api/v1/competitions/'. $testCompetition->id, [
        'category_id'=> 100
    ])->assertJsonValidationErrors(['category_id']);
    
})->with(['administrador', 'organizador']);

//Test #17 can't update a competition if not admin
it ("can't update a competition if not admin", function (string $role) {
    $user = User::factory()->create();
    $user->assignRole($role);
    $this->actingAs($user);

    $testCompetition = Competition::create([
        "name" => "Test Competition",
        "description" => "Test description",
        "prize_information" => "Test prize information",
        "tools_information" => "Test tools information",
        "max_teams" => 10,
        "start_date" => "2026-07-04",
        "end_date" => "2026-07-06",
        "admin_id" => test()->user->id,
        "category_id" => test()->category->id,
    ]);

    $this->putJson('api/v1/competitions/'. $testCompetition->id, [
        'is_finished'=> 1
    ])->assertStatus(403);
    
})->with(['administrador', 'organizador']);

//Test #18 can't update a competition if not authorized
it ("can't update a competition if not authorized", function (string $role) {
    $user = User::factory()->create();
    $user->assignRole($role);
    $this->actingAs($user);

    $testCompetition = Competition::create([
        "name" => "Test Competition",
        "description" => "Test description",
        "prize_information" => "Test prize information",
        "tools_information" => "Test tools information",
        "max_teams" => 10,
        "start_date" => "2026-07-04",
        "end_date" => "2026-07-06",
        "admin_id" => $user->id,
        "category_id" => test()->category->id,
    ]);

    $this->putJson('api/v1/competitions/'. $testCompetition->id, [
        'is_finished'=> 1
    ])->assertStatus(403);
    
})->with(['lider', 'participante']);

//Test #19 Id not found
it ("can't update a competition with a wrong id", function () {
    $user = User::factory()->create();
    $this->actingAs($user);
    $this->putJson('api/v1/competitions/1000')
        ->assertStatus(404);
});

//Test #20 can update a competition
it ("can update a competition", function (string $role) {
    $user = User::factory()->create();
    $user->assignRole($role);
    $this->actingAs($user);

    $testCompetition = Competition::create([
        "name" => "Test Competition",
        "description" => "Test description",
        "prize_information" => "Test prize information",
        "tools_information" => "Test tools information",
        "max_teams" => 10,
        "start_date" => "2026-07-04",
        "end_date" => "2026-07-06",
        "admin_id" => $user->id,
        "category_id" => test()->category->id,
    ]);

    $this->putJson('api/v1/competitions/'. $testCompetition->id, [
        'is_finished'=> 1,
        'start_date'=> "2026-08-01",
        "end_date"=> "2026-08-04",
    ])->assertStatus(200)->assertJsonStructure([
        "message",
        "data" => [
            "id",
            "name",
            "description",
            "prize",
            "tools",
            "maximum_number_of_teams",
            "total_number_of_teams",
            "registration_start_date",
            "registration_closing_date",
            "competition_is_finished",
            "category",
            "admin",
            "teams",
            "modules"
        ]
    ])->assertJsonFragment([
        'competition_is_finished'=>true,
        "registration_start_date"=>"2026-08-01",
        "registration_closing_date"=>"2026-08-04",
    ]);
    
})->with(['administrador', 'organizador']);

//Test #21 can't delete a competition if not authorized
it ("can't delete competitions if not authorized", function (string $role) {
    $user = User::factory()->create();
    $user->assignRole($role);
    $this->actingAs($user);

    $testCompetition = Competition::create([
        "name" => "Test Competition",
        "description" => "Test description",
        "prize_information" => "Test prize information",
        "tools_information" => "Test tools information",
        "max_teams" => 10,
        "start_date" => "2026-07-04",
        "end_date" => "2026-07-06",
        "admin_id" => test()->user->id,
        "category_id" => test()->category->id,
    ]);

    $this->deleteJson('api/v1/competitions/'.$testCompetition->id)
        ->assertStatus(403);

})->with(['organizador', 'participante', 'lider']);

//Test #22 Id not found
it ("can't delete competition with a wrong id", function () {
    $user = User::factory()->create();
    $this->actingAs($user);
    $this->deleteJson('api/v1/competitions/1000')
        ->assertStatus(404);
});

//Test #23 can delete a competition
it ("can delete competitions", function (string $role) {
    $user = User::factory()->create();
    $user->assignRole($role);
    $this->actingAs($user);

    $testCompetition = Competition::create([
        "name" => "Test Competition",
        "description" => "Test description",
        "prize_information" => "Test prize information",
        "tools_information" => "Test tools information",
        "max_teams" => 10,
        "start_date" => "2026-07-04",
        "end_date" => "2026-07-06",
        "admin_id" => $user->id,
        "category_id" => test()->category->id,
    ]);

    $this->deleteJson('api/v1/competitions/'.$testCompetition->id)
        ->assertStatus(200);

})->with(['administrador']);