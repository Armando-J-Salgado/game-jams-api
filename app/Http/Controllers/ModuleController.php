<?php

namespace App\Http\Controllers;

use App\Models\Competition;
use App\Models\Module;
use App\Http\Requests\StoreModuleRequest;
use App\Http\Requests\UpdateModuleRequest;
use App\Http\Resources\ModuleResource;
use App\Http\Requests\IndexModuleRequest;
use App\Models\Handover;
use Illuminate\Support\Facades\Auth;
use function Termwind\parse;

class ModuleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(IndexModuleRequest $request)
    {
        $this->authorize('viewAny', Module::class);
        $query = Module::query();

        if($request->input('title')) {
            $query->where('title', 'like', '%' . $request->title . '%');
        }

        if($request->input('due_date')) {
            $query->whereDate('due_date', '<=', $request->due_date);
        }

        if($request->input('competition_id')) {
            $query->where('competition_id', intval($request->competition_id));
        }

        if ($request->has('is_trashed') && $request->is_trashed) {
            $query->onlyTrashed();
        }

        $modules = $query->paginate($request->input('per_page', 15), page: $request->input('page', 1))->getCollection();
        return response()->json(ModuleResource::collection($modules));
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
    public function store(StoreModuleRequest $request)
    {
        $this->authorize('create', Module::class);

        $user = Auth::user();
        $competition = Competition::find($request->input('competition_id'));

        if ($user->id !== $competition->admin_id) {
            return response()->json(['Error'=>'You can`t create modules in a third-party competition'], 403);
        }
        
        $module = Module::create([
            'title'=>$request->title,
            'description'=>$request->description,
            'attachments'=> $request->attachments ?? null,
            'due_date'=>$request->due_date,
            'competition_id'=>$request->competition_id,
        ]);

        //Regla de negocio: Al crear un módulo en la competencia todos los equipos
        //deben ser asignados una entrega en relación a ese módulo
        $teams = $module->competition->teams;
        foreach ($teams as $team) {
            Handover::create([
                'title'=> 'Asignación módulo: ' . $module->title,
                'module_id'=>$module->id,
                'team_id'=>$team->id,
            ]);
        }

        return response()->json([
            'message'=>'Module created successfully',
            'module'=> ModuleResource::make($module)
        ], 201);


    }

    /**
     * Display the specified resource.
     */
    public function show(Module $module)
    {
        $this->authorize('view', $module);
        return response()->json([ModuleResource::make($module)], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Module $module)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateModuleRequest $request, Module $module)
    {
        $this->authorize('update', $module);

        $user = Auth::user();

        if($module->competition->admin_id !== $user->id) {
            return response()->json(['Error'=>'You can`t modify a third party`s competition module'], 403);
        }

        $data = $request->validated();
        $module->update($data);

        return response()->json([
            'message'=>'Module updated successfully',
            ModuleResource::make($module)
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Module $module)
    {
        $this->authorize('delete', $module);
        $module->delete();

        return response()->json(['message'=>'Modulo eliminado de forma exitosa'], 200);
    }
}
