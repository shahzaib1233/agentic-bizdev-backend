<?php

use App\Http\Controllers\Auth\AuthController;
// use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


// Route::apiResource('post', PostController::class);
Route::post('register',[AuthController::class,'register']);
Route::post('login',[AuthController::class,'login']);
Route::post('Logout',[AuthController::class,'Logout'])->middleware('auth:sanctum');
Route::put('User/update/{id}',[AuthController::class,'update'])->middleware('auth:sanctum');

Route::middleware(['auth', 'sanctum'])->group(function () {
    
});
