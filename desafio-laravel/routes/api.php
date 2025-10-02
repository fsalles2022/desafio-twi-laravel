<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/users', [\App\Http\Controllers\UserController::class, 'index']);
Route::get('/users/{id}', [\App\Http\Controllers\UserController::class, 'show']);
Route::put('/users/{id}', [\App\Http\Controllers\UserController::class, 'update']);
Route::post('/users', [\App\Http\Controllers\UserController::class, 'store']);
Route::delete('/users/{id}', [\App\Http\Controllers\UserController::class, 'destroy']);
// Endpoint de teste de microserviço vivo
Route::get('/health', fn() => ['status' => 'ok']);

// Endpoint que consome microserviço Node.js
Route::get('/external', [\App\Http\Controllers\UserController::class, 'externalService']);
