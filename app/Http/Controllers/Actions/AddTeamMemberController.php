<?php

namespace App\Http\Controllers\Actions;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Models\Team;
use App\Models\User;

class AddTeamMemberController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, Team $team, User $user)
    {
        // Enforce policy via Gate
        Gate::authorize('manage', $team);

        // Check if user is already in a team
        if ($user->team_id !== null) {
            if ($user->team_id === $team->id) {
                return response()->json(['message' => 'This user is already in your team.'], 400);
            }
            return response()->json(['message' => 'This user is already a member of another team.'], 400);
        }

        // Check if team has space
        if ($team->total_members >= $team->max_members) {
            return response()->json(['message' => 'The team has reached its maximum capacity.'], 400);
        }

        // Add the user to the team
        $user->update(['team_id' => $team->id]);
        
        // Increase total_members
        $team->increment('total_members');

        // Ensure user has 'participante' role
        if (!$user->hasRole('participante')) {
            $user->assignRole('participante');
        }

        return response()->json([
            'message' => 'Member added successfully to the team.',
        ], 201);
    }
}
