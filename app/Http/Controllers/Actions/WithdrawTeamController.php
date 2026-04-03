<?php

namespace App\Http\Controllers\Actions;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Models\Team;
use App\Models\Competition;

class WithdrawTeamController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, Team $team, Competition $competition)
    {
        // Enforce policy via Gate
        Gate::authorize('manage', $team);

        // Check if actually enrolled
        if (!$competition->teams()->where('team_id', $team->id)->exists()) {
            return response()->json(['message' => 'The team is not enrolled in this competition.'], 400);
        }

        // Withdraw team
        $competition->teams()->detach($team->id);
        
        if ($competition->total_teams > 0) {
            $competition->decrement('total_teams');
        }

        return response()->json([
            'message' => 'Team withdrawn successfully from the competition. Handovers are kept as history.',
        ]);
    }
}
