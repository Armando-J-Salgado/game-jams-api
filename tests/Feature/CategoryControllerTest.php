<?php

use App\Models\User;
use App\Models\Category;
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

#Test: It can get a list of all categories
test("It can get a list of all categories", function (string $role) {
    /** @var \Tests\TestCase $this */
    $user = User::factory()->create();
    $user->assignRole($role);
    $this->actingAs($user, 'sanctum');

    Category::factory(3)->create();

    $response = $this->getJson('api/v1/categories');

    $response->assertStatus(200)
             ->assertJsonCount(3, 'data');
})->with(['administrador', 'organizador', 'lider', 'participante']);

#Test: It refuses to show the list of categories to unauthorized users
test("It cannot show the list of categories to unauthorized users", function (string $role) {
    /** @var \Tests\TestCase $this */
    $user = User::factory()->create();
    if ($role !== 'invitado') {
        $user->assignRole($role);
    }
    $this->actingAs($user, 'sanctum');

    $response = $this->getJson('api/v1/categories');

    $response->assertStatus(403);
})->with(['invitado']);

// ==========================================
// STORE TESTS
// ==========================================

#Test: Successfully create a category with valid user types
test("It can create a category", function (string $role) {
    /** @var \Tests\TestCase $this */
    $user = User::factory()->create();
    $user->assignRole($role);
    $this->actingAs($user, 'sanctum');

    $categoryData = [
        'name' => 'New Game Category',
    ];

    $response = $this->postJson('api/v1/categories', $categoryData);

    $response->assertStatus(201);
    $this->assertDatabaseHas('categories', ['name' => 'New Game Category']);
})->with(['administrador']);

#Test: Refusal to create a category with unauthorized user role
test("It cannot create a category with unauthorized user role", function (string $role) {
    /** @var \Tests\TestCase $this */
    $user = User::factory()->create();
    if ($role !== 'invitado') {
        $user->assignRole($role);
    }
    $this->actingAs($user, 'sanctum');

    $response = $this->postJson('api/v1/categories', [
        'name' => 'Secret Category',
    ]);

    $response->assertStatus(403);
})->with(['organizador', 'lider', 'participante', 'invitado']);

#Test: Refusal to create category with missing required data
test("It cannot create a category with missing required data", function (string $role) {
    /** @var \Tests\TestCase $this */
    $user = User::factory()->create();
    $user->assignRole($role);
    $this->actingAs($user, 'sanctum');

    $categoryData = []; // Missing 'name'

    $response = $this->postJson('api/v1/categories', $categoryData);

    $response->assertStatus(422)
             ->assertJsonValidationErrors(['name']);
})->with(['administrador']);

#Test: Refusal to create category with wrong format data
test("It cannot create a category with wrong format data", function (string $role) {
    /** @var \Tests\TestCase $this */
    $user = User::factory()->create();
    $user->assignRole($role);
    $this->actingAs($user, 'sanctum');

    $categoryData = [
        'name' => str_repeat('A', 256), // Invalid: exceeds standard max:255 limit
    ];

    $response = $this->postJson('api/v1/categories', $categoryData);

    $response->assertStatus(422)
             ->assertJsonValidationErrors(['name']);
})->with(['administrador']);

// ==========================================
// SHOW TESTS
// ==========================================

#Test: Successfully shows details of an existing category to a valid user
test("It can show details of an existing category", function (string $role) {
    /** @var \Tests\TestCase $this */
    $user = User::factory()->create();
    $user->assignRole($role);
    $this->actingAs($user, 'sanctum');

    $category = Category::factory()->create();

    $response = $this->getJson("api/v1/categories/{$category->id}");

    $response->assertStatus(200)
             ->assertJsonPath('data.id', $category->id)
             ->assertJsonPath('data.name', $category->name);
})->with(['administrador', 'organizador', 'lider', 'participante']);

#Test: Doesn't show details to unauthorized users
test("It cannot show category details to unauthorized users", function (string $role) {
    /** @var \Tests\TestCase $this */
    $user = User::factory()->create();
    if ($role !== 'invitado') {
        $user->assignRole($role);
    }
    $this->actingAs($user, 'sanctum');

    $category = Category::factory()->create();

    $response = $this->getJson("api/v1/categories/{$category->id}");

    $response->assertStatus(403);
})->with(['invitado']);

#Test: No category found
test("It can return a not found error when attempting to show a non-existent category", function (string $role) {
    /** @var \Tests\TestCase $this */
    $user = User::factory()->create();
    $user->assignRole($role);
    $this->actingAs($user, 'sanctum');

    $response = $this->getJson('api/v1/categories/99999');

    $response->assertStatus(404);
})->with(['administrador']);

// ==========================================
// UPDATE TESTS
// ==========================================

#Test: Successfully updates data of a category with valid users
test("It can update a category", function (string $role) {
    /** @var \Tests\TestCase $this */
    $user = User::factory()->create();
    $user->assignRole($role);
    $this->actingAs($user, 'sanctum');

    $category = Category::factory()->create();

    $updateData = [
        'name' => 'Updated Category Name',
    ];

    $response = $this->putJson("api/v1/categories/{$category->id}", $updateData);

    $response->assertStatus(200);
    $this->assertDatabaseHas('categories', [
        'id' => $category->id,
        'name' => 'Updated Category Name'
    ]);
})->with(['administrador']);

#Test: Refuses to update data with unauthorized users
test("It cannot update a category with unauthorized user roles", function (string $role) {
    /** @var \Tests\TestCase $this */
    $user = User::factory()->create();
    if ($role !== 'invitado') {
        $user->assignRole($role);
    }
    $this->actingAs($user, 'sanctum');

    $category = Category::factory()->create();

    $response = $this->putJson("api/v1/categories/{$category->id}", [
        'name' => 'Unauthorized Update',
    ]);

    $response->assertStatus(403);
})->with(['organizador', 'lider', 'participante', 'invitado']);

#Test: Refuses to update a category with wrong format data
test("It cannot update a category with wrong format data", function (string $role) {
    /** @var \Tests\TestCase $this */
    $user = User::factory()->create();
    $user->assignRole($role);
    $this->actingAs($user, 'sanctum');

    $category = Category::factory()->create();

    $response = $this->putJson("api/v1/categories/{$category->id}", [
        'name' => str_repeat('A', 256), // Invalid: over max 255 limit
    ]);

    $response->assertStatus(422)
             ->assertJsonValidationErrors(['name']);
})->with(['administrador']);

#Test: Refuses to update data of a non-existent category
test("It can return a not found error when updating a non-existent category", function (string $role) {
    /** @var \Tests\TestCase $this */
    $user = User::factory()->create();
    $user->assignRole($role);
    $this->actingAs($user, 'sanctum');

    $response = $this->putJson('api/v1/categories/99999', [
        'name' => 'Ghost Category'
    ]);

    $response->assertStatus(404);
})->with(['administrador']);

#Test: Successfully partially updates data of a category with valid users (PATCH)
test("It can partially update a category", function (string $role) {
    /** @var \Tests\TestCase $this */
    $user = User::factory()->create();
    $user->assignRole($role);
    $this->actingAs($user, 'sanctum');

    $category = Category::factory()->create();

    $updateData = [
        'name' => 'Patch Updated Category Name',
    ];

    $response = $this->patchJson("api/v1/categories/{$category->id}", $updateData);

    $response->assertStatus(200);
    $this->assertDatabaseHas('categories', [
        'id' => $category->id,
        'name' => 'Patch Updated Category Name'
    ]);
})->with(['administrador']);

#Test: Refuses to partially update data with unauthorized users (PATCH)
test("It cannot partially update a category with unauthorized user roles", function (string $role) {
    /** @var \Tests\TestCase $this */
    $user = User::factory()->create();
    if ($role !== 'invitado') {
        $user->assignRole($role);
    }
    $this->actingAs($user, 'sanctum');

    $category = Category::factory()->create();

    $response = $this->patchJson("api/v1/categories/{$category->id}", [
        'name' => 'Unauthorized Partial Update',
    ]);

    $response->assertStatus(403);
})->with(['organizador', 'lider', 'participante', 'invitado']);

#Test: Refuses to partially update a category with wrong format data (PATCH)
test("It cannot partially update a category with wrong format data", function (string $role) {
    /** @var \Tests\TestCase $this */
    $user = User::factory()->create();
    $user->assignRole($role);
    $this->actingAs($user, 'sanctum');

    $category = Category::factory()->create();

    $response = $this->patchJson("api/v1/categories/{$category->id}", [
        'name' => str_repeat('A', 256), // Invalid: over max 255 limit
    ]);

    $response->assertStatus(422)
             ->assertJsonValidationErrors(['name']);
})->with(['administrador']);

#Test: Refuses to partially update data of a non-existent category (PATCH)
test("It can return a not found error when partially updating a non-existent category", function (string $role) {
    /** @var \Tests\TestCase $this */
    $user = User::factory()->create();
    $user->assignRole($role);
    $this->actingAs($user, 'sanctum');

    $response = $this->patchJson('api/v1/categories/99999', [
        'name' => 'Ghost Category Update'
    ]);

    $response->assertStatus(404);
})->with(['administrador']);

// ==========================================
// DELETE TESTS
// ==========================================

#Test: Successful logical deletion of a category
test("It can logically delete a category", function (string $role) {
    /** @var \Tests\TestCase $this */
    $user = User::factory()->create();
    $user->assignRole($role);
    $this->actingAs($user, 'sanctum');

    $category = Category::factory()->create();

    $response = $this->deleteJson("api/v1/categories/{$category->id}");

    $response->assertStatus(200);
    $this->assertSoftDeleted('categories', ['id' => $category->id]);
})->with(['administrador']);

#Test: Refuses to logically delete a category with unauthorized users
test("It cannot logically delete a category with unauthorized user roles", function (string $role) {
    /** @var \Tests\TestCase $this */
    $user = User::factory()->create();
    if ($role !== 'invitado') {
        $user->assignRole($role);
    }
    $this->actingAs($user, 'sanctum');

    $category = Category::factory()->create();

    $response = $this->deleteJson("api/v1/categories/{$category->id}");

    $response->assertStatus(403);
})->with(['organizador', 'lider', 'participante', 'invitado']);

#Test: Refusal to soft delete a non-existent category
test("It can return a not found error when deleting a non-existent category", function (string $role) {
    /** @var \Tests\TestCase $this */
    $user = User::factory()->create();
    $user->assignRole($role);
    $this->actingAs($user, 'sanctum');

    $response = $this->deleteJson('api/v1/categories/99999');

    $response->assertStatus(404);
})->with(['administrador']);
