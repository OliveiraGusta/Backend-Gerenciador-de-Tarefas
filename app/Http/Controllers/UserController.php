<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    public function getUser(Request $request){
        $user = $request->user();

        if (!$user) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'githubUsername' => $user->githubUsername,
            'is_admin' => $user->is_admin,

        ], 200);
    }
    
    public function updateUser(Request $request) {
        $user = $request->user();
    
        if (!$user) {
            return response()->json(["error" => "User not authenticated"], 401);
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
    
    public function deleteUser(Request $request){
        $user = $request->user();

        if (!$user) {
            return response()->json(["error" => "User not authenticated"], 401);
        }

        $user->delete();
        return response()->json(["message" => "User deleted successfully"], 200);
    }

    //ADMIN
    public function adminGetAllUsers() {
        $users = User::all();
    
        return response()->json([
            'message' => 'All users retrieved successfully',
            'users' => $users
        ], 200);
    }
    
    public function adminGetUser($userId) {
        $user = User::find($userId);
    
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }
    
        return response()->json([
            'message' => 'User retrieved successfully',
            'user' => $user
        ], 200);
    }
    
    public function adminDeleteUser($userId) {
        $user = User::find($userId);
    
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }
    
        $user->delete();
    
        return response()->json([
            'message' => 'User deleted successfully'
        ], 200);
    }

}
