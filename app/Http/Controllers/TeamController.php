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
     * Display a listing of the resource.
     */
    public function index()
    {
        return TeamResource::collection(Team::all());
    }

    /**
     * Store a newly created resource in storage.
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
     * Display the specified resource.
     */
    public function show(Team $team)
    {
        return new TeamResource($team);
    }

    /**
     * Update the specified resource in storage.
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
     * Remove the specified resource from storage.
     */
    public function destroy(Team $team)
    {
        $team->delete();

        return response()->json([
            'message' => 'Team deleted successfully'
        ], 204);
    }

    /**
     * Display a listing of soft deleted resources.
     */
    public function deleted()
    {
        $deletedTeams = Team::onlyTrashed()->get();
        return TeamResource::collection($deletedTeams);
    }

    /**
     * Restore a soft deleted resource.
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
