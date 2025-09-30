<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::prefix('api')->group(function () {
    Route::get('/health', fn() => response()->json(['status' => 'ok']));
    Route::apiResource('users', UserController::class);
    Route::get('/external', [UserController::class, 'externalService']);
});
