<?php

namespace App\Http\Controllers\Actions;

use App\Http\Controllers\Controller;
use App\Http\Resources\EnrollmentResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Models\Team;
use App\Models\Competition;
use App\Models\Handover;

class EnrollTeamController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, Team $team, Competition $competition)
    {
        // Enforce policy via Gate
        Gate::authorize('manage', $team);

        // Check if already enrolled
        if ($competition->teams()->where('team_id', $team->id)->exists()) {
            return response()->json(['message' => 'The team is already enrolled in this competition.'], 400);
        }

        // Check if competition has space
        if ($competition->total_teams >= $competition->max_teams) {
            return response()->json(['message' => 'The competition has reached its maximum team capacity.'], 400);
        }

        // Check that registration period has opened (start_date)
        if (now()->toDateString() < $competition->start_date) {
            return response()->json(['message' => 'Competition registration has not started yet.'], 400);
        }

        // Check dates (closed or finished)
        if ($competition->is_finished || now()->toDateString() > $competition->end_date) {
            return response()->json(['message' => 'This competition is closed or finished.'], 400);
        }

        // Enroll team
        $competition->teams()->attach($team->id);
        $competition->increment('total_teams');

        // Create empty handovers for each module
        foreach ($competition->modules as $module) {
            Handover::create([
                'title' => 'Entrega: ' . $module->title . ' - ' . $team->name,
                'is_delivered' => false,
                'score' => null,
                'module_id' => $module->id,
                'team_id' => $team->id,
            ]);
        }

        // Reload relationships for the resource
        $team->load('users');
        $competition->load('modules');

        return (new EnrollmentResource(['team' => $team, 'competition' => $competition]))
            ->response()
            ->setStatusCode(201)
            ->header('Content-Type', 'application/json');
    }
}
