<?php

namespace App\Http\Controllers\Actions;

use App\Http\Controllers\Controller;
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

        // Check dates (closed or active)
        if ($competition->is_finished || now()->toDateString() > $competition->end_date) {
            return response()->json(['message' => 'This competition is closed or finished.'], 400);
        }

        // Enroll team
        $competition->teams()->attach($team->id);
        $competition->increment('total_teams');

        // Create empty handovers
        foreach ($competition->modules as $module) {
            Handover::create([
                'title' => 'Entrega: ' . $module->name . ' - ' . $team->name,
                'is_delivered' => false,
                'score' => null,
                'module_id' => $module->id,
                'team_id' => $team->id,
            ]);
        }

        return response()->json([
            'message' => 'Team enrolled successfully in the competition.',
        ], 201);
    }
}
