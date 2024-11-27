<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:6',
                'githubUsername' => 'nullable|string|max:255',
                'is_admin' => 'nullable|boolean',
            ]);

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'githubUsername' => $validated['githubUsername'] ?? null,
                'is_admin' => $validated['is_admin'] ?? false,
            ]);

            $user->makeHidden('password');

            return response()->json([
                "message" => "User created successfully",
                "user" => $user
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                "message" => "Registration failed",
                "error" => $e->getMessage()
            ], 500);
        }
    }


    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (!$user) {
            return response()->json(["error" => "User not found"], 404);
        }

        if (!Hash::check($validated['password'], $user->password)) {
            return response()->json(["error" => "Invalid password"], 401);
        }

        $token = $user->createToken("auth_token")->plainTextToken;

        return response()->json([
            "access_token" => $token,
            "token_type" => 'Bearer',
        ]);
    }
}
