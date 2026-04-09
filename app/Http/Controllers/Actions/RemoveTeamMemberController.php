<?php

namespace App\Http\Controllers\Actions;

use App\Http\Controllers\Controller;
use App\Http\Resources\TeamResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Models\Team;
use App\Models\User;

class RemoveTeamMemberController extends Controller
{
    /**
     * Remove Team Member
     *
     * Removes a user from a specific team.
     *
     * @group Team Actions
     * @authenticated
     *
     * @urlParam team int required The ID of the team. Example: 1
     * @urlParam user int required The ID of the user to remove. Example: 2
     *
     * @response 200 {
     *   "message": "Member removed successfully from the team."
     * }
     */
    public function __invoke(Request $request, Team $team, User $user)
    {
        // Enforce policy via Gate
        Gate::authorize('manage', $team);

        // Leader cannot remove themselves
        if ($user->id === $team->admin_id) {
            return response()->json(['message' => 'The team leader cannot be removed. You must delete the team or transfer leadership.'], 400);
        }

        // Check if user is actually in this team
        if ($user->team_id !== $team->id) {
            return response()->json(['message' => 'This user is not a member of the specified team.'], 400);
        }

        // Remove the user from the team
        $user->update(['team_id' => null]);
        
        // Decrease total_members
        $team->decrement('total_members');

        return response()->json([
            'message' => 'Member removed successfully from the team.',
            'data' => new TeamResource($team->fresh()),
        ]);
    }
}
