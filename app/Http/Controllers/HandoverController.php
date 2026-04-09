<?php

namespace App\Http\Controllers;

use App\Models\Handover;
use App\Http\Requests\StoreHandoverRequest;
use App\Http\Requests\UpdateHandoverRequest;
use App\Http\Resources\HandoverResource;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HandoverController extends Controller
{
    use AuthorizesRequests;
    public function __construct()
    {
        $this->authorizeResource(Handover::class);
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Handover::class);

        $request->validate([
            'team_id'        => 'integer|exists:teams,id',
            'module_id'      => 'integer|exists:modules,id',
            'competition_id' => 'integer|exists:competitions,id',
        ]);

        $query = Handover::query();

        // Enforce team isolation for non-admins
        $user = $request->user();
        if (!$user->hasAnyRole(['administrador', 'organizador'])) {
            $query->where('team_id', $user->team_id);
        }

        // If filtering by competition, verify that the team is enrolled in it
        if ($request->has('competition_id') && $request->has('team_id')) {
            $teamId = $request->integer('team_id');
            $competitionId = $request->integer('competition_id');

            $enrolled = DB::table('competition_team')
                ->where('team_id', $teamId)
                ->where('competition_id', $competitionId)
                ->exists();

            if (!$enrolled) {
                return response()->json([
                    'message' => 'The specified team is not enrolled in this competition.',
                ], 404);
            }
        }

        $query
            ->when(
                $request->has('team_id'),
                fn($q) => $q->where('team_id', $request->input('team_id'))
            )
            ->when(
                $request->has('module_id'),
                fn($q) => $q->where('module_id', $request->input('module_id'))
            )
            ->when(
                $request->has('competition_id'),
                fn($q) => $q->whereHas(
                    'module',
                    fn($mq) => $mq->where('competition_id', $request->input('competition_id'))
                )
            );

        return HandoverResource::collection($query->get());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreHandoverRequest $request)
    {
        $this->authorize('create', Handover::class);
        $data = $request->validated();

        $handover = Handover::create($data);

        return response()->json(HandoverResource::make($handover), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Handover $handover)
    {
        $this->authorize('view', $handover);
        return HandoverResource::make($handover);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Handover $handover)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateHandoverRequest $request, Handover $handover)
    {
        $this->authorize('update', $handover);
        $data = $request->validated();
        $handover->update($data);
        return HandoverResource::make($handover);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Handover $handover)
    {
        $this->authorize('delete', $handover);
        $handover->delete();
        return response()->json(['message' => 'Handover deleted successfully']);
    }
}
