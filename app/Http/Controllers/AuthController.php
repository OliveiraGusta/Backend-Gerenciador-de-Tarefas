<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string',
            'githubUsername' => 'required|string|max:255',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'githubUsername' => $validated['githubUsername'] ?? null,
        ]);

        $user->makeHidden('password');

        return response()->json([
            "message" => "User created successfully",
            "user" => $user
        ], 201);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = $request->user();
            $token = $user->createToken("auth_token")->plainTextToken;

            return response()->json([
                "access_token" => $token,
                "token_type" => 'Bearer'
            ]);
        }

        return response()->json(["error" => "Invalid credentials"], 401);
    }
}
