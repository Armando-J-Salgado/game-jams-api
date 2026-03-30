<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Routes that do not require Authentication
Route::post('v1/auth/login', [AuthController::class, "login"]);


Route::middleware('auth:sanctum')->prefix('v1')->group(function() {

    // Routes that require Authentication
    // Authtentication routes
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/profile', [AuthController::class, 'profile']);

    // CRUD users



    // CRUD Categories

    

    // CRUD Handovers



    // CRUD Utils

    

    // CRUD Teams



    // CRUD Competitions

    

    // CRUD Modules
});

