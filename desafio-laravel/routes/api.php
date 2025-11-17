<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\VideoController;
use App\Http\Controllers\CourseController;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Response;

// Login / Logout
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

// Rotas de usuários (proteção com Sanctum)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/users', [\App\Http\Controllers\UserController::class, 'index']);
    Route::get('/users/{id}', [\App\Http\Controllers\UserController::class, 'show']);
    Route::put('/users/{id}', [\App\Http\Controllers\UserController::class, 'update']);
    Route::post('/users', [\App\Http\Controllers\UserController::class, 'store']);
    Route::delete('/users/{id}', [\App\Http\Controllers\UserController::class, 'destroy']);

    // course
    Route::get('/course', [CourseController::class, 'index']);
    Route::get('/course/{id}', [CourseController::class, 'show']);
    Route::post('/course', [CourseController::class, 'store']);
    Route::put('/course/{course}', [CourseController::class, 'update']);
    Route::delete('/course/{course}', [CourseController::class, 'destroy']);


    // Vídeos (banco de dados)
    Route::get('/videos', [VideoController::class, 'index']);          
    Route::get('/videos/user', [VideoController::class, 'userVideos']); 
    Route::get('/videos/{id}', [VideoController::class, 'show']);      
    Route::post('/videos', [VideoController::class, 'store']);         
    Route::put('/videos/{id}', [VideoController::class, 'update']);    
    Route::delete('/videos/{id}', [VideoController::class, 'destroy']); 
    Route::post('/videos/{id}/watched', [VideoController::class, 'markWatched']); 

    // Streaming proxy (videos do Node)
    Route::get('/video/{filename}', [VideoController::class, 'stream']);

    // Listar vídeos diretamente da pasta Node
    Route::get('/videos-node', function () {
        $files = array_filter(scandir(storage_path('../video-service/videos')), fn($f) => !in_array($f, ['.', '..']));
        $videos = array_map(fn($f) => [
            'name' => $f,
            'url'  => url("/api/video/{$f}"),
        ], $files);
        return response()->json(array_values($videos));
    });
});
