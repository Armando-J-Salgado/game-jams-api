<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CompetitionController;
use App\Http\Controllers\HandoverController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Actions\EnrollTeamController;
use App\Http\Controllers\Actions\WithdrawTeamController;
use App\Http\Controllers\Actions\AddTeamMemberController;
use App\Http\Controllers\Actions\RemoveTeamMemberController;
use App\Http\Controllers\Actions\SubmitHandoverController;
use Illuminate\Support\Facades\Route;

// Routes that do not require Authentication
Route::post('v1/auth/login', [AuthController::class, "login"]);


Route::middleware('auth:sanctum')->prefix('v1')->group(function () {

    // Routes that require Authentication
    // Authtentication routes
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/profile', [AuthController::class, 'profile']);

    // CRUD users
    Route::get('/users', [UserController::class, 'index']);
    Route::post('/users', [UserController::class, 'store']);
    Route::get('/users/{user}', [UserController::class, 'show'])
        ->missing(fn() => response()->json(['message' => 'There are no matches for the searched user'], 404));
    Route::put('/users/{user}', [UserController::class, 'update'])
        ->missing(fn() => response()->json(['message' => 'There are no matches for the searched user'], 404));
    Route::patch('/users/{user}', [UserController::class, 'update'])
        ->missing(fn() => response()->json(['message' => 'There are no matches for the searched user'], 404));
    Route::delete('/users/{user}', [UserController::class, 'destroy'])
        ->missing(fn() => response()->json(['message' => 'There are no matches for the searched user'], 404));


    // CRUD Categories
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/categories/{category}', [CategoryController::class, 'show'])
        ->missing(fn() => response()
            ->json(['message' => 'There are no matches for the searched category'], 404));
    Route::post('/categories', [CategoryController::class, 'store']);
    Route::put('/categories/{category}', [CategoryController::class, 'update'])
        ->missing(fn() => response()
            ->json(['message' => 'There are no matches for the searched category'], 404));
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])
        ->missing(fn() => response()
            ->json(['message' => 'There are no matches for the searched category'], 404));


    // CRUD Handovers
    Route::get('/handovers', [HandoverController::class, 'index']);
    Route::get('/handovers/{handover}', [HandoverController::class, 'show'])
        ->missing(fn() => response()
            ->json(['message' => 'There are no matches for the searched handover'], 404));
    Route::post('/handovers', [HandoverController::class, 'store']);
    Route::put('/handovers/{handover}', [HandoverController::class, 'update'])
        ->missing(fn() => response()
            ->json(['message' => 'There are no matches for the searched handover'], 404));
    Route::delete('/handovers/{handover}', [HandoverController::class, 'destroy'])
        ->missing(fn() => response()
            ->json(['message' => 'There are no matches for the searched handover'], 404));

    // Business Logic Actions (Utils)
    Route::post('/teams/{team}/enroll/{competition}', EnrollTeamController::class);
    Route::delete('/teams/{team}/withdraw/{competition}', WithdrawTeamController::class);
    Route::post('/teams/{team}/members/{user}', AddTeamMemberController::class);
    Route::delete('/teams/{team}/members/{user}', RemoveTeamMemberController::class);
    Route::patch('/handovers/{handover}', SubmitHandoverController::class);
    // CRUD Teams
    Route::get('/teams', [TeamController::class, 'index']);
    Route::post('/teams', [TeamController::class, 'store']);
    Route::get('/teams/deleted', [TeamController::class, 'deleted'])
    ->missing(fn () => response()->json(['message' => 'There are no matches for the searched team'], 404));
    Route::patch('/teams/{id}/restore', [TeamController::class, 'restore'])
    ->missing(fn () => response()->json(['message' => 'There are no matches for the searched team'], 404));
    Route::get('/teams/{team}', [TeamController::class, 'show'])
    ->missing(fn () => response()->json(['message' => 'There are no matches for the searched team'], 404));
    Route::put('/teams/{team}', [TeamController::class, 'update'])
    ->missing(fn () => response()->json(['message' => 'There are no matches for the searched team'], 404));
    Route::delete('/teams/{team}', [TeamController::class, 'destroy'])
    ->missing(fn () => response()->json(['message' => 'There are no matches for the searched team'], 404));
    
        // CRUD Competitions
    Route::post('/competitions', [CompetitionController::class, 'store']);
    Route::get('/competitions', [CompetitionController::class, 'index']);
    Route::get('/competitions/{competition}', [CompetitionController::class, 'show'])
        ->missing(fn() => response()->json(['message' => 'There are no matches for the searched competition'], 404));
    Route::patch('/competitions/{competition}', [CompetitionController::class, 'update'])
        ->missing(fn() => response()->json(['message' => 'There are no matches for the searched competition'], 404));
    Route::put('/competitions/{competition}', [CompetitionController::class, 'update'])
        ->missing(fn() => response()->json(['message' => 'There are no matches for the searched competition'], 404));
    Route::delete('/competitions/{competition}', [CompetitionController::class, 'destroy'])
        ->missing(fn() => response()->json(['message' => 'There are no matches for the searched competition'], 404));

    // CRUD Modules
    Route::post('/modules', [ModuleController::class, 'store']);
    Route::get('/modules', [ModuleController::class, 'index']);
    Route::get('/modules/{module}', [ModuleController::class, 'show'])
        ->missing(fn() => response()->json(['message' => 'There are no matches for the searched competition'], 404));
    Route::put('/modules/{module}', [ModuleController::class, 'update'])
        ->missing(fn() => response()->json(['message' => 'There are no matches for the searched competition'], 404));
    Route::patch('/modules/{module}', [ModuleController::class, 'update'])
        ->missing(fn() => response()->json(['message' => 'There are no matches for the searched competition'], 404));
    Route::delete('/modules/{module}', [ModuleController::class, 'destroy'])
        ->missing(fn() => response()->json(['message' => 'There are no matches for the searched competition'], 404));
});

