<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    /**
     * User Login
     *
     * Authenticates a user and returns a token.
     *
     * @group Authentication
     * @bodyParam email string required The user email. Example: admin@example.com
     * @bodyParam password string required The user password. Example: password
     *
     * @response 200 {
     *  "status": "success",
     *  "messsage": "Login successful",
     *  "token": "1|abcdef123456789...",
     *  "user": {"id": 1, "name": "Admin", "email": "admin@example.com"}
     * }
     * @response 422 {
     *  "message": "Email or password incorrect"
     * }
     */
    public function login(Request $request)
    {
        if (Auth::attempt($request->only('email', 'password'))) {
            $user = Auth::user();
            $token = $user->createToken('token-name');

            return response()->json([
                'status' => 'success',
                'messsage' => 'Login successful',
                'token' => $token->plainTextToken,
                'user' => Auth::user(),
            ]);
        }

        return response()->json([
            'message' => 'Email or password incorrect',
        ], 422);
    }

    /**
     * User Logout
     *
     * Revokes the current access token.
     *
     * @group Authentication
     * @authenticated
     *
     * @response 200 {
     *  "status": "success",
     *  "message": "Logout successful"
     * }
     */
    public function logout(Request $request) {
        $user = Auth::user();
        $user->tokens()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Logout successful',
        ]);
    }

    /**
     * User Profile
     *
     * Retrieves the currently authenticated user's profile.
     *
     * @group Authentication
     * @authenticated
     *
     * @response 200 {
     *  "profile": {"id": 1, "name": "Admin", "email": "admin@example.com"}
     * }
     */
    public function profile(Request $request) {
        return response()->json([
            'profile' => Auth::user(),
        ]);
    }
}
