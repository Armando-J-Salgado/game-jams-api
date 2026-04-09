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
    /**
     * List Users
     *
     * Display a listing of the users.
     *
     * @group Users
     * @authenticated
     *
     * @queryParam role string Filter by role. Example: participante
     * @queryParam team_id int Filter by team ID. Example: 1
     * @queryParam dui string Filter by exact DUI. Example: 12345678-9
     * @queryParam name string Filter by partial name. Example: Juan
     * @queryParam lastname string Filter by partial lastname. Example: Perez
     * @queryParam email string Filter by partial email. Example: juan@
     * @queryParam username string Filter by partial username. Example: juano
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "name": "Juan",
     *       "lastname": "Perez",
     *       "email": "juan@example.com",
     *       "username": "juanp",
     *       "dui": "12345678-9"
     *     }
     *   ]
     * }
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', User::class);

        $request->validate([
            'role' => 'string|exists:roles,name',
            'team_id' => 'integer|exists:teams,id',
            'dui' => 'string|regex:/^\d{8}-\d$/',
        ]);

        $users = User::query()
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
    /**
     * Create User
     *
     * Store a newly created user in storage.
     *
     * @group Users
     * @authenticated
     *
     * @bodyParam name string required The name of the user. Example: Juan
     * @bodyParam lastname string required The lastname of the user. Example: Perez
     * @bodyParam email string required The email of the user. Example: juanperez@example.com
     * @bodyParam username string required The username of the user. Example: juanp
     * @bodyParam password string required The password of the user (min 8 chars). Example: secretpassword
     * @bodyParam dui string required The DUI of the user. Example: 12345678-9
     * @bodyParam role string required The role to assign (administrador, organizador, lider, participante). Example: participante
     *
     * @response 201 {
     *   "id": 1,
     *   "name": "Juan",
     *   "lastname": "Perez",
     *   "email": "juanperez@example.com"
     * }
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
    /**
     * Get User
     *
     * Display the specified user.
     *
     * @group Users
     * @authenticated
     *
     * @urlParam user int required The ID of the user. Example: 1
     *
     * @response 200 {
     *   "id": 1,
     *   "name": "Juan",
     *   "email": "juanperez@example.com"
     * }
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
    /**
     * Update User
     *
     * Update the specified user in storage.
     *
     * @group Users
     * @authenticated
     *
     * @urlParam user int required The ID of the user. Example: 1
     * @bodyParam name string nullable The name of the user. Example: Juan Carlos
     * @bodyParam lastname string nullable The lastname of the user. Example: Perez
     * @bodyParam username string nullable The username of the user. Example: juanc
     * @bodyParam password string nullable The new password (min 8 chars). Example: newpassword
     * @bodyParam role string nullable The role to assign. Example: lider
     *
     * @response 200 {
     *   "id": 1,
     *   "name": "Juan Carlos"
     * }
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
    /**
     * Delete User
     *
     * Remove the specified user from storage.
     *
     * @group Users
     * @authenticated
     *
     * @urlParam user int required The ID of the user. Example: 1
     *
     * @response 200 {
     *   "message": "User deleted successfully"
     * }
     */
    public function destroy(User $user)
    {
        $this->authorize('delete', $user);
        $user->delete();
        return response()->json(['message' => 'User deleted successfully'], 200);
    }
}
