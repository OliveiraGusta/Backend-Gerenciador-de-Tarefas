<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;


class TaskController extends Controller
{   
    public function index(Request $request)
    {
        $tasks = Task::all();  

        return response()->json($tasks); 
    }

    public function create(Request $request)
    {
        $user = $request->user(); 

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|string',
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

    public function getTask(Request $request, $userId, $taskId)
    {
        $user = $request->user();

        if ($user->id != $userId) {
            return response()->json(["error" => "Unauthorized"], 403);
        }

        $task = Task::where('id', $taskId)->where('userOwner', $userId)->first();

        if (!$task) {
            return response()->json(["error" => "Task not found"], 404);
        }

        return response()->json($task, 200);
    }

    public function update(Request $request, $userId, $taskId)
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
            'status' => 'nullable|string',
        ]);

        // Atualiza a task com os dados validados
        $task->update($validated);

        return response()->json([
            "message" => "Task updated successfully",
            "task" => $task
        ], 200);
    }

    public function delete(Request $request, $userId, $taskId)
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

}
