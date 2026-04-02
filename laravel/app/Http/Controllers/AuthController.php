<?php

namespace App\Http\Controllers;

use App\Models\Administrator;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            "full_name" => "required",
            "username" => "required|min:3|unique:users,username",
            "password" => "required|min:6"
        ]);

        $user = User::create([
            "full_name" => $validated['full_name'],
            "username" => $validated['username'],
            "password" => bcrypt($validated['password']),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        $user->token = $token;

        return response()->json([
            "status" => "success",
            "message" => "User registration successful",
            "data" => $user
        ], 200);
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            "username" => "required",
            "password" => "required"
        ]);

        $admins = Administrator::where('username', $validated['username'])->first();
        if ($admins) {
            if (Hash::check($validated['password'], $admins['password'])) {
                $token = $admins->createToken('auth_token')->plainTextToken;
                $admins->role = 'admin';
                $admins->token = $token;
                return response()->json([
                    "status" => "success",
                    "message" => "Login successfull",
                    "data" => $admins
                ], 200);
            }
        }

        $user = User::where('username', $validated['username'])->first();

        if (!$user || !Hash::check($validated['password'], $user['password'])) {
            return response()->json([
                "status" => "authentication_failed",
                "message" =>  "The username or password you entered is incorrect"
            ], 401);
        }

        $user->role = 'user';
        $user->token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            "status" => "success",
            "message" => "Login successfull",
            "data" => $user
        ], 200);
    }

    public function logout(Request $request)
    {
        $user = $request->user();

        $user->currentAccessToken()->delete();
    }
}
