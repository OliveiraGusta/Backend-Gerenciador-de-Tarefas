<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function show($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(["error" => "User not found"], 404);
        }

        return response()->json($user);
    }

    public function delete(Request $request, $id)
{
    $user = User::find($id);

    if (!$user) {
        return response()->json(["error" => "User not found"], 404);
    }

    if ($request->user()->id !== (int) $id) {
        return response()->json(["error" => "Unauthorized"], 403);
    }
    $user->delete();

    return response()->json(["message" => "User deleted successfully"], 200);
}

    public function update(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(["error" => "User not found"], 404);
        }

        if ($request->user()->id !== (int) $id) {
            return response()->json(["error" => "Unauthorized"], 403);
        }

        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6',
            'githubUsername' => 'nullable|string|max:255',
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $user->update($validated);

        return response()->json([
            "message" => "User updated successfully",
            "user" => $user
        ], 200);
    }
}
