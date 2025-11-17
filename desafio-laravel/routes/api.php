<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\VideoController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\UserController;

// Login / Logout
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

Route::middleware('auth:sanctum')->group(function () {

    // USERS
    Route::prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index']);
        Route::post('/', [UserController::class, 'store']);
        Route::get('/{id}', [UserController::class, 'show']);
        Route::put('/{id}', [UserController::class, 'update']);
        Route::delete('/{id}', [UserController::class, 'destroy']);
    });

    // COURSES
    Route::prefix('courses')->group(function () {
        Route::get('/', [CourseController::class, 'index']);
        Route::post('/', [CourseController::class, 'store']);
        Route::get('/{course}', [CourseController::class, 'show']);
        Route::put('/{course}', [CourseController::class, 'update']);
        Route::delete('/{course}', [CourseController::class, 'destroy']);
    });

    // VIDEOS
    Route::prefix('videos')->group(function () {
        Route::get('/', [VideoController::class, 'index']);
        Route::post('/', [VideoController::class, 'store']);
        Route::get('/user', [VideoController::class, 'userVideos']);
        Route::get('/{id}', [VideoController::class, 'show']);
        Route::put('/{id}', [VideoController::class, 'update']);
        Route::delete('/{id}', [VideoController::class, 'destroy']);
        Route::post('/{id}/watched', [VideoController::class, 'markWatched']);
    });

    // Streaming do Node
    Route::get('/video/stream/{filename}', [VideoController::class, 'stream']);

    // Listar vídeos da pasta Node
    Route::get('/videos-node', function () {
        $files = array_filter(scandir(storage_path('../video-service/videos')), fn($f) => !in_array($f, ['.', '..']));
        $videos = array_map(fn($f) => [
            'name' => $f,
            'url'  => url("/api/video/stream/{$f}"),
        ], $files);

        return response()->json(array_values($videos));
    });
});
