<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexCompetitionRequest;
use App\Http\Resources\CompetitionResource;
use App\Models\Competition;
use App\Http\Requests\StoreCompetitionRequest;
use App\Http\Requests\UpdateCompetitionRequest;
use Illuminate\Support\Facades\Auth;

class CompetitionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(IndexCompetitionRequest $request)
    {
        $query = Competition::query();

        if ($request->input('name')) {
            $query->where('name', 'like','%' . $request->name . '%');
        }

        if ($request->input('start_date')) {
            $query->whereDate('start_date', '>=', $request->start_date);
        }

        if ($request->input('end_date')){
            $query->whereDate('end_date', '<=', $request->end_date);
        }

        if ($request->has('is_finished')) {
            $query->where('is_finished', $request->is_finished);
        }

        if ($request->has('is_trashed') && $request->is_trashed) {
            $query->onlyTrashed();
        }

        $competitions = $query->paginate($request->input('per_page', 15), page: $request->input('page', 1))->getCollection();
        return CompetitionResource::collection($competitions);
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
    public function store(StoreCompetitionRequest $request)
    {
        $user = Auth::user();
        $competition = Competition::create([
            'name'=> $request->name,
            'description'=> $request->description,
            'prize_information'=> $request->prize_information,
            'tools_information'=>$request->tools_information,
            'max_teams'=> !$request->max_teams ? 20 : $request->max_teams,
            'start_date'=> $request->start_date,
            'end_date'=>$request->end_date,
            'category_id'=>$request->category_id,
            'admin_id'=>$user->id,
        ]);

        return response()->json([
            'message'=>'Competition created succesfully',
            'competition'=> new CompetitionResource($competition),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Competition $competition)
    {
        return CompetitionResource::make($competition);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Competition $competition)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCompetitionRequest $request, Competition $competition)
    {
        $data = $request->validated();
        $competition->update($data);

        return response()->json([
            'message'=> 'Competition updated succesfully',
            'data'=> CompetitionResource::make($competition)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Competition $competition)
    {
        $competition->delete();

        return response()->json(['message'=>'Competition deleted succesfully'], 200);
    }
}
