<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class AuthController extends Controller
{
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

    public function logout(Request $request) {
        $user = Auth::user();
        $user->tokens()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Logout successful',
        ]);
    }

    public function profile(Request $request) {
        return response()->json([
            'profile' => Auth::user(),
        ]);
    }
}
