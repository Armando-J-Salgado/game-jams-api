<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Http\Requests\StoreTeamRequest;
use App\Http\Requests\UpdateTeamRequest;
use App\Http\Resources\TeamResource;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TeamController extends Controller
{
    use AuthorizesRequests;

    /**
     * List Teams
     *
     * Display a listing of the teams.
     *
     * @group Teams
     * @authenticated
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "name": "Los Codificadores",
     *       "max_members": 5,
     *       "total_members": 3
     *     }
     *   ]
     * }
     * @response 401 {
     *   "message": "Unauthenticated."
     * }
     */
    public function index()
    {
        return TeamResource::collection(Team::all());
    }

    /**
     * Create Team
     *
     * Store a newly created team in storage.
     *
     * @group Teams
     * @authenticated
     *
     * @bodyParam name string required The name of the team. Example: Los Codificadores
     * @bodyParam admin_id int required The ID of the user creating/leading the team. Example: 1
     * @bodyParam max_members int nullable The maximum number of members. Example: 5
     *
     * @response 201 {
     *   "message": "Team created successfully",
     *   "data": {
     *     "id": 1,
     *     "name": "Los Codificadores",
     *     "total_members": 1
     *   }
     * }
     * @response 401 {
     *   "message": "Unauthenticated."
     * }
     * @response 403 {
     *   "message": "This action is unauthorized."
     * }
     * @response 422 {
     *   "message": "The given data was invalid.",
     *   "errors": {"name": ["The name has already been taken."]}
     * }
     */
    public function store(StoreTeamRequest $request)
    {
        $this->authorize('create', Team::class);

        $data = $request->validated();
        $data['total_members'] = 1; // El líder es el primer miembro
        $data['max_members'] = 5; // el máximo default debe ser 5

        $team = Team::create($data);

        return response()->json([
            'message' => 'Team created successfully',
            'data' => new TeamResource($team)
        ], 201);
    }

    /**
     * Get Team
     *
     * Display the specified team.
     *
     * @group Teams
     * @authenticated
     *
     * @urlParam team int required The ID of the team. Example: 1
     *
     * @response 200 {
     *   "id": 1,
     *   "name": "Los Codificadores"
     * }
     * @response 401 {
     *   "message": "Unauthenticated."
     * }
     * @response 404 {
     *   "message": "There are no matches for the searched team"
     * }
     */
    public function show(Team $team)
    {
        return new TeamResource($team);
    }

    /**
     * Update Team
     *
     * Update the specified team in storage. Applies to both PUT and PATCH requests.
     *
     * @group Teams
     * @authenticated
     *
     * @urlParam team int required The ID of the team. Example: 1
     * @bodyParam name string nullable The new name of the team. Example: Los Pro
     * @bodyParam admin_id int nullable The ID of the new team leader. Example: 2
     * @bodyParam max_members int nullable The new maximum members. Example: 4
     *
     * @response 200 {
     *   "message": "Team updated successfully",
     *   "data": {
     *     "id": 1,
     *     "name": "Los Pro",
     *     "admin_id": 2,
     *     "max_members": 4,
     *     "total_members": 2
     *   }
     * }
     * @response 401 {
     *   "message": "Unauthenticated."
     * }
     * @response 403 {
     *   "message": "This action is unauthorized."
     * }
     * @response 404 {
     *   "message": "There are no matches for the searched team"
     * }
     * @response 422 {
     *   "message": "The given data was invalid.",
     *   "errors": {"name": ["The name has already been taken."]}
     * }
     */
    public function update(UpdateTeamRequest $request, Team $team)
    {
        $team->update($request->validated());

        return response()->json([
            'message' => 'Team updated successfully',
            'data' => new TeamResource($team)
        ]);
    }

    /**
     * Delete Team
     *
     * Soft-delete the specified team.
     *
     * @group Teams
     * @authenticated
     *
     * @urlParam team int required The ID of the team. Example: 1
     *
     * @response 204 {"message": "Team deleted successfully"}
     * @response 401 {
     *   "message": "Unauthenticated."
     * }
     * @response 403 {
     *   "message": "This action is unauthorized."
     * }
     * @response 404 {
     *   "message": "There are no matches for the searched team"
     * }
     */
    public function destroy(Team $team)
    {
        $team->delete();

        return response()->json([
            'message' => 'Team deleted successfully'
        ], 204);
    }

    /**
     * List Deleted Teams
     *
     * Display a listing of softly deleted teams.
     *
     * @group Teams
     * @authenticated
     *
     * @response 200 {
     *   "data": [
     *     {"id": 1, "name": "Team Viejo"}
     *   ]
     * }
     * @response 401 {
     *   "message": "Unauthenticated."
     * }
     * @response 403 {
     *   "message": "This action is unauthorized."
     * }
     */
    public function deleted()
    {
        $deletedTeams = Team::onlyTrashed()->get();
        return TeamResource::collection($deletedTeams);
    }

    /**
     * Restore Team
     *
     * Restore a soft-deleted team by ID.
     *
     * @group Teams
     * @authenticated
     *
     * @urlParam id int required The ID of the deleted team. Example: 1
     *
     * @response 200 {
     *   "message": "Team restored successfully",
     *   "data": {"id": 1, "name": "Team Viejo"}
     * }
     * @response 401 {
     *   "message": "Unauthenticated."
     * }
     * @response 403 {
     *   "message": "This action is unauthorized."
     * }
     * @response 404 {
     *   "message": "There are no matches for the searched team"
     * }
     */
    public function restore($id)
    {
        $team = Team::onlyTrashed()->find($id);
        
        if (!$team) {
            return response()->json(['message' => 'There are no matches for the searched team'], 404);
        }

        $team->restore();

        return response()->json([
            'message' => 'Team restored successfully',
            'data' => new TeamResource($team)
        ]);
    }
}
