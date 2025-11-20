<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\VideoController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\UserController;

// -------------------------
//  LOGIN / LOGOUT
// -------------------------
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);


// -------------------------
//  ROTAS PROTEGIDAS
// -------------------------
Route::middleware('auth:sanctum')->group(function () {

    // -------------------------
    //  USERS
    // -------------------------
    Route::prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index']);
        Route::post('/', [UserController::class, 'store']);
        Route::get('/{id}', [UserController::class, 'show']);
        Route::put('/{id}', [UserController::class, 'update']);
        Route::delete('/{id}', [UserController::class, 'destroy']);
    });


    // -------------------------
    //  COURSES (acesso geral)
    // -------------------------
    Route::prefix('courses')->group(function () {

        Route::get('/', [CourseController::class, 'index']);
        Route::get('/{course}', [CourseController::class, 'show']);

        // Vídeos do curso
        Route::get('/{course}/videos', [CourseController::class, 'videos']);
    });


    // -------------------------
    //  COURSES (somente TEACHER)
    // -------------------------
    Route::middleware('role:teacher')->group(function () {

        Route::prefix('courses')->group(function () {
            Route::post('/', [CourseController::class, 'store']);
            Route::put('/{course}', [CourseController::class, 'update']);
            Route::delete('/{course}', [CourseController::class, 'destroy']);
        });
    });


    // -------------------------
    //  ROTA DE TESTE (roles)
    // -------------------------
    Route::get('/me', function () {
        return [
            'user'  => auth()->user(),
            'roles' => auth()->user()->getRoleNames()
        ];
    });


    // -------------------------
    //  VIDEOS
    // -------------------------
    Route::prefix('videos')->group(function () {

        Route::get('/', [VideoController::class, 'index']);
        Route::get('/user', [VideoController::class, 'userVideos']);
        Route::get('/{id}', [VideoController::class, 'show']);

        // somente professor pode criar/editar/deletar vídeos
        Route::middleware('role:teacher')->group(function () {
            Route::post('/', [VideoController::class, 'store']);
            Route::put('/{id}', [VideoController::class, 'update']);
            Route::delete('/{id}', [VideoController::class, 'destroy']);
        });

        // aluno marca como assistido
        Route::post('/{id}/watched', [VideoController::class, 'markWatched']);
    });


    // -------------------------
    //  STREAMING DO NODE
    // -------------------------
    Route::get('/video/stream/{filename}', [VideoController::class, 'stream']);


    // -------------------------
    //  LISTA DE VÍDEOS DIRETO DA PASTA DO NODE
    // -------------------------
    Route::get('/videos-node', function () {
        $path = storage_path('../video-service/videos');
        $files = array_filter(scandir($path), fn($f) => !in_array($f, ['.', '..']));

        $videos = array_map(fn($f) => [
            'name' => $f,
            'url'  => url("/api/video/stream/{$f}")
        ], $files);

        return response()->json(array_values($videos));
    });
});
