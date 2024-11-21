<?php
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;

//Test Api
Route::get('/', function () {
    return response()->json(true);
});

// Auth routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// User routes (protected)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::get('/users/{id}', [UserController::class, 'show']);
    Route::delete('/users/{id}', [UserController::class, 'delete']);
    Route::put('/users/{id}', [UserController::class, 'update']);
});
