<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class UserController extends Controller
{


    use AuthorizesRequests;
    public function __construct()
    {
        $this->authorizeResource(User::class);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', User::class);

        $request->validate([
            'role' => 'string|exists:roles,name',
            'team_id' => 'integer|exists:teams,id',
            'dui' => 'string|regex:/^\d{8}-\d$/',
            'status' => 'string|in:active,deleted,all',
        ]);

        $users = User::query();

        if ($request->input('status') === 'deleted') {
            $users->onlyTrashed();
        }

        if ($request->input('status') === 'all') {
            $users->withTrashed();
        }

        $users = $users
            ->when($request->filled('role'), function ($query) use ($request) {
                $query->whereHas('roles', fn($q) => $q->where('name', $request->input('role')));
            })
            ->when($request->filled('team_id'), function ($query) use ($request) {
                $query->where('team_id', $request->input('team_id'));
            })
            ->when($request->filled('name'), function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->input('name') . '%');
            }) 
            ->when($request->filled('lastname'), function ($query) use ($request) {
                $query->where('lastname', 'like', '%' . $request->input('lastname') . '%');
            })
            ->when($request->filled('email'), function ($query) use ($request) {
                $query->where('email', 'like', '%' . $request->input('email') . '%');
            })
            ->when($request->filled('username'), function ($query) use ($request) {
                $query->where('username', 'like', '%' . $request->input('username') . '%');
            })
            ->when($request->filled('dui'), function ($query) use ($request) {
                $query->where('dui', $request->input('dui'));
            })
            ->get();

        return UserResource::collection($users);
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
    public function store(StoreUserRequest $request)
    {
        $this->authorize('create', User::class);

        $validatedData = $request->validated();
        $user = User::create([
            'name' => $validatedData['name'],
            'lastname' => $validatedData['lastname'],
            'email' => $validatedData['email'],
            'username' => $validatedData['username'],
            'password' => bcrypt($validatedData['password']),
            'dui' => $validatedData['dui'],
        ]);

        $user->assignRole($validatedData['role']);

        return (new UserResource($user))
            ->response()
            ->setStatusCode(201);

    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $this->authorize('view', $user);
        return UserResource::make($user);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $this->authorize('update', $user);
        $validated = $request->validated();

        if (!empty($validated['password'])) {
            $validated['password'] = bcrypt($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        if (!empty($validated['role'])) {
            $user->syncRoles([$validated['role']]);
        }

        return UserResource::make($user);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $this->authorize('delete', $user);
        $user->delete();
        return response()->json(['message' => 'User deleted successfully'], 200);
    }

    /**
     * Display a listing of soft deleted resources.
     */
    public function deleted()
    {
        $this->authorize('viewAny', User::class);

        $users = User::onlyTrashed()->get();
        return UserResource::collection($users);
    }

    /**
     * Restore a soft deleted resource.
     */
    public function restore(string $id)
    {
        $user = User::onlyTrashed()->find($id);

        if (!$user) {
            return response()->json(['message' => 'There are no matches for the searched user'], 404);
        }

        $this->authorize('restore', $user);
        $user->restore();

        return response()->json([
            'message' => 'User restored successfully',
            'data' => UserResource::make($user),
        ], 200);
    }

    /**
     * Permanently delete a soft deleted resource.
     */
    public function forceDelete(string $id)
    {
        $user = User::onlyTrashed()->find($id);

        if (!$user) {
            return response()->json(['message' => 'There are no matches for the searched user'], 404);
        }

        $this->authorize('forceDelete', $user);
        $user->forceDelete();

        return response()->json(['message' => 'User permanently deleted successfully'], 200);
    }
}
