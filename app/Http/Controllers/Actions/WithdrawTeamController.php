<?php

namespace App\Http\Controllers\Actions;

use App\Http\Controllers\Controller;
use App\Http\Resources\TeamResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Models\Team;
use App\Models\Competition;

class WithdrawTeamController extends Controller
{
    /**
     * Withdraw Team
     *
     * Withdraws a team from a competition.
     *
     * @group Team Actions
     * @authenticated
     *
     * @urlParam team int required The ID of the team. Example: 1
     * @urlParam competition int required ID of the competition. Example: 1
     *
     * @response 200 {
     *   "message": "Team withdrawn successfully from the competition. Handovers are kept as history.",
     *   "data": {"id": 1, "name": "Los Codificadores"}
     * }
     * @response 400 {
     *   "message": "The team is not enrolled in this competition."
     * }
     * @response 401 {
     *   "message": "Unauthenticated."
     * }
     * @response 403 {
     *   "message": "This action is unauthorized."
     * }
     * @response 404 {
     *   "message": "Team or competition not found."
     * }
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
            'data' => new TeamResource($team->fresh()),
        ]);
    }
}
