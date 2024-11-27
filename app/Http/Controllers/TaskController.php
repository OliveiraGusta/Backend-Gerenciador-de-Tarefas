<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;


class TaskController extends Controller
{   
    public function index(Request $request, $userId)
    {
        $user = $request->user();

        if ($user->id != $userId) {
            return response()->json(["error" => "Unauthorized"], 403);
        }

        $tasks = Task::where('userOwner', $userId)->get();

        return response()->json($tasks, 200);
    }

    public function getTask(Request $request, $userId, $taskId)
    {
        $user = $request->user();

        if ($user->id != $userId) {
            return response()->json(["error" => "Unauthorized"], 403);
        }

        $task = Task::where('id', operator: $taskId)->where('userOwner', $userId)->first();

        if (!$task) {
            return response()->json(["error" => "Task not found"], 404);
        }

        return response()->json($task, 200);
    }

    public function createTask(Request $request)
    {
        $user = $request->user(); 

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|integer',
        ]);

        $task = Task::create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'status' => $validated['status'],
            'userOwner' => $user->id,
        ]);

        return response()->json([
            "message" => "Task created successfully",
            "task" => $task
        ], 201);
    }

    public function updateTask(Request $request, $userId, $taskId)
    {
        $authenticatedUser = $request->user();

        if ($authenticatedUser->id != $userId && !$authenticatedUser->is_admin) {
            return response()->json(["error" => "Unauthorized"], 403);
        }

        $task = Task::where('id', $taskId)->where('userOwner', $userId)->first();

        if (!$task) {
            return response()->json(["error" => "Task not found"], 404);
        }

        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'status' => 'nullable|integer',
        ]);

        $task->update($validated);

        return response()->json([
            "message" => "Task updated successfully",
            "task" => $task
        ], 200);
    }

    public function deleteTask(Request $request, $userId, $taskId)
    {
        $authenticatedUser = $request->user();

        if ($authenticatedUser->id != $userId && !$authenticatedUser->is_admin) {
            return response()->json(["error" => "Unauthorized"], 403);
        }

        $task = Task::where('id', $taskId)->where('userOwner', $userId)->first();
        if (!$task) {
            return response()->json(["error" => "Task not found"], 404);
        }
        $task->delete();

        return response()->json([
            "message" => "Task deleted successfully"
        ], 200);
    }

    //ADMIN 
    public function adminGetAllTasks(Request $request)
    {
        if (!$request->user() || !$request->user()->is_admin) {
            return response()->json(['error' => 'Access denied'], 403);
        }

        $tasks = Task::with('user:id,name,email,githubUsername')->get();

        return response()->json($tasks, 200);
    }

    public function adminGetTask($taskId)
    {
        $task = Task::with('user')->find($taskId);

        if (!$task) {
            return response()->json(['message' => 'Task not found'], 404);
        }

        return response()->json($task, 200);
    }

    public function adminUpdateTask(Request $request, $taskId)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'status' => 'nullable|integer',
        ]);
    
        $task = Task::find($taskId);

        if (!$task) {
            return response()->json(["error" => "Task not found"], 404);
        }

        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'status' => 'nullable|integer',
        ]);

        $task->update($validated);

        return response()->json([
            "message" => "Task updated successfully",
            "task" => $task
        ], 200);


    }
    
    public function adminDeleteTask($taskId)
    {
        $task = Task::find($taskId);

        if (!$task) {
            return response()->json(['message' => 'Task not found'], 404);
        }

        $task->delete();

        return response()->json(['message' => 'Task deleted successfully'], 200);
    }


}
