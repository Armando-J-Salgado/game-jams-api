<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

// Test #1 it returns token with valid credentials
test('login returns token with valid credentials', function () {
	$password = 'password123';
	$user = User::factory()->create([
		'password' => Hash::make($password),
	]);

	$response = $this->postJson('/api/v1/auth/login', [
		'email' => $user->email,
		'password' => $password,
	]);

	$response
		->assertStatus(200)
		->assertJsonStructure([
			'status',
			'messsage',
			'token',
			'user' => ['id', 'email'],
		]);
});

// Test #2 it rejects invalid credentials
test('login fails with invalid credentials', function () {
	$user = User::factory()->create([
		'password' => Hash::make('password123'),
	]);

	$response = $this->postJson('/api/v1/auth/login', [
		'email' => $user->email,
		'password' => 'wrong-password',
	]);

	$response
		->assertStatus(422)
		->assertJson([
			'message' => 'Email or password incorrect',
		]);
});

// Test #3 it blocks profile without auth
test('profile requires authentication', function () {
	$this->getJson('/api/v1/auth/profile')
		->assertStatus(401);
});

// Test #4 it returns profile for authenticated user
test('profile returns authenticated user', function () {
	$user = User::factory()->create();
	$token = $user->createToken('token-name')->plainTextToken;

	$this->getJson('/api/v1/auth/profile', [
		'Authorization' => 'Bearer ' . $token,
	])
		->assertStatus(200)
		->assertJsonPath('profile.id', $user->id);
});

// Test #5 it revokes tokens on logout
test('logout revokes user tokens', function () {
	$user = User::factory()->create();
	$token = $user->createToken('token-name')->plainTextToken;

	$this->postJson('/api/v1/auth/logout', [], [
		'Authorization' => 'Bearer ' . $token,
	])
		->assertStatus(200)
		->assertJson([
			'status' => 'success',
			'message' => 'Logout successful',
		]);

	$this->assertSame(0, $user->tokens()->count());
});