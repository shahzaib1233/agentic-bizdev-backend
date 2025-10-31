<?php

use App\Http\Controllers\Agents\AgentsController;
use App\Http\Controllers\Auth\AuthController;
// use Illuminate\Http\Request;
use App\Http\Controllers\BusinessDetails\BusinessDetailsController;
use App\Http\Controllers\ChatHistory\ChatHistoryController;
use Illuminate\Support\Facades\Route;

// Route::apiResource('post', PostController::class);
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::post('Logout', [AuthController::class, 'Logout'])->middleware('auth:sanctum');
Route::patch('User/update/{id}', [AuthController::class, 'update'])->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('user', [AuthController::class, 'GetAllUsers']);
    Route::prefix('agents')->group(function () {
        Route::get('/', [AgentsController::class, 'GetAllAgents']);
        Route::post('/', [AgentsController::class, 'create']);
        Route::patch('/{id}', [AgentsController::class, 'update']);
        Route::delete('/{id}', [AgentsController::class, 'destroy']);
    });

    // business detail
    Route::prefix('clients')->group(function () {
        Route::get('/', [BusinessDetailsController::class, 'GetAllClients']);
        Route::get('/{id}', [BusinessDetailsController::class, 'show']);
        Route::post('', [BusinessDetailsController::class, 'create']);
        Route::patch('/{id}', [BusinessDetailsController::class, 'update']);
        Route::patch('/{id}', [BusinessDetailsController::class, 'update']);
        Route::delete('/{id}', [BusinessDetailsController::class, 'destroy']);
    });

    // Chat id
    Route::prefix('chat-histories')->group(function () {
        Route::post('/', [ChatHistoryController::class, 'store']);
        Route::get('/', [ChatHistoryController::class, 'getAll']);
    });
});
