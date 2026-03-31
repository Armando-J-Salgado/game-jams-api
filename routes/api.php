<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\HandoverController;
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
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/categories/{category}', [CategoryController::class, 'show'])
    ->missing(fn () => response()
    ->json(['message' => 'There are no matches for the searched category'], 404));;
    Route::post('/categories', [CategoryController::class, 'store']);
    Route::put('/categories/{category}', [CategoryController::class, 'update'])
    ->missing(fn () => response()
    ->json(['message' => 'There are no matches for the searched category'], 404));;

    

    // CRUD Handovers
    


    // CRUD Utils

    

    // CRUD Teams



    // CRUD Competitions

    

    // CRUD Modules
});

