<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json(true);
});

// Auth routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {

    // User Routes
    Route::get('/user', [UserController::class, 'getUser']);
    Route::put('/user', [UserController::class, 'updateUser']); 
    Route::delete('/user', [UserController::class, 'deleteUser']); 

    // Task Routes
    Route::prefix('users/{userId}/tasks')->group(function () {
        Route::get('/', [TaskController::class, 'index']);
        Route::post('/', [TaskController::class, 'createTask']);
        Route::get('/{taskId}', [TaskController::class, 'getTask']);
        Route::put('/{taskId}', [TaskController::class, 'updateTask']);
        Route::delete('/{taskId}', [TaskController::class, 'deleteTask']);
    });

    // Admin Routes
    Route::middleware('admin')->prefix('admin')->group(function () {
        // User Admin Routes
        Route::get('/users', [UserController::class, 'adminGetAllUsers']);
        Route::get('/users/{userId}', [UserController::class, 'adminGetUser']); 
        Route::delete('/users/{userId}', [UserController::class, 'adminDeleteUser']);

        // Task Admin Routes
        Route::get('/tasks', [TaskController::class, 'adminGetAllTasks']); 
        Route::get('/tasks/{taskId}', [TaskController::class, 'adminGetTask']); 
        Route::put('/tasks/{taskId}', [TaskController::class, 'adminUpdateTask']); 
        Route::delete('/tasks/{taskId}', [TaskController::class, 'adminDeleteTask']);
    });
});

