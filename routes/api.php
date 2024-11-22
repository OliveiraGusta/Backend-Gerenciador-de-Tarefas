<?php 
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TaskController;

Route::get('/', function () {
    return response()->json(true);
});

// Auth routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);


Route::middleware('auth:sanctum')->group(function () {
    // User Routes
    Route::get('/user', [UserController::class, 'getUser']);
    Route::put('/user', [UserController::class, 'update']); 
    Route::delete('/user', [UserController::class, 'delete']); 

    // Task Routes
    Route::prefix('users/{userId}/tasks')->group(function () {
        Route::get('/', [TaskController::class, 'index']);
        Route::post('/', [TaskController::class, 'create']);
        Route::get('/{taskId}', [TaskController::class, 'getTask']);
        Route::put('/{taskId}', [TaskController::class, 'update']);
        Route::delete('/{taskId}', [TaskController::class, 'delete']);
    });
});
