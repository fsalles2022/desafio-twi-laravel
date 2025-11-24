<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\VideoController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\UserController;


/*
|--------------------------------------------------------------------------
| AUTH
|--------------------------------------------------------------------------
*/

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/refresh', [AuthController::class, 'refresh']); // refresh token

    Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);
});

/*
|--------------------------------------------------------------------------
| ROTAS PROTEGIDAS
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | USERS (somente teacher)
    |--------------------------------------------------------------------------
    */
    Route::prefix('users')->middleware('role:teacher')->group(function () {
        Route::get('/', [UserController::class, 'index']);
        Route::post('/', [UserController::class, 'store']);
        Route::get('/{id}', [UserController::class, 'show']);
        Route::put('/{id}', [UserController::class, 'update']);
        Route::delete('/{id}', [UserController::class, 'destroy']);
    });

    /*
    |--------------------------------------------------------------------------
    | COURSES – acesso geral
    |--------------------------------------------------------------------------
    */
    Route::prefix('courses')->group(function () {
        Route::get('/', [CourseController::class, 'index']);
        Route::get('/{course}', [CourseController::class, 'show']);
        Route::get('/{course}/videos', [CourseController::class, 'videos']);

        // Vídeos assistidos pelo aluno
        Route::get('/{course}/watched', [CourseController::class, 'watchedVideos'])
            ->middleware('role:student');

        // Auto matrícula
        Route::post('/{course}/enroll', [CourseController::class, 'selfEnroll'])
            ->middleware('role:student');
    });

    /*
    |--------------------------------------------------------------------------
    | COURSES – CRUD apenas teacher
    |--------------------------------------------------------------------------
    */
    Route::prefix('courses')->middleware('role:teacher')->group(function () {
        Route::post('/', [CourseController::class, 'store']);
        Route::put('/{course}', [CourseController::class, 'update']);
        Route::delete('/{course}', [CourseController::class, 'destroy']);
    });

    /*
    |--------------------------------------------------------------------------
    | VIDEOS
    |--------------------------------------------------------------------------
    */
    Route::prefix('videos')->group(function () {
        Route::get('/', [VideoController::class, 'index']);
        Route::get('/user', [VideoController::class, 'userVideos']);
        Route::get('/{id}', [VideoController::class, 'show']);

        // CRUD (somente teacher)
        Route::middleware('role:teacher')->group(function () {
            Route::post('/', [VideoController::class, 'store']);
            Route::put('/{id}', [VideoController::class, 'update']);
            Route::delete('/{id}', [VideoController::class, 'destroy']);
        });

        // Student marca e desmarca assistido
        Route::post('/{id}/watched', [VideoController::class, 'markAsWatched'])
            ->middleware('role:student');
        Route::delete('/{id}/watched', [VideoController::class, 'unmarkAsWatched'])
            ->middleware('role:student');
    });

    /*
    |--------------------------------------------------------------------------
    | STREAM DO NODE
    |--------------------------------------------------------------------------
    */
    Route::get('/video/stream/{filename}', [VideoController::class, 'stream']);

    /*
    |--------------------------------------------------------------------------
    | LISTA DE VÍDEOS NA PASTA
    |--------------------------------------------------------------------------
    */
    Route::get('/videos-node', function () {
        $path = storage_path('../video-service/videos');

        $files = array_filter(
            scandir($path),
            fn($file) => !in_array($file, ['.', '..'])
        );

        $videos = array_map(
            fn($file) => [
                'name' => $file,
                'url'  => url("/api/video/stream/{$file}"),
            ],
            $files
        );

        return response()->json(array_values($videos));
    });

    // routes/api.php
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/profile', [UserController::class, 'profile']);
        Route::put('/profile', [UserController::class, 'updateProfile']);
    });
});
