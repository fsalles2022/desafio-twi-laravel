<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\VideoController;

// Login e logout
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

Route::middleware('auth:sanctum')->get('/video/{filename}', function ($filename) {
    $nodeUrl = "http://localhost:4000/stream/{$filename}";

    try {
        $response = Http::withHeaders([
            'Range' => request()->header('Range'),
        ])->get($nodeUrl);

        return Response::make($response->body(), $response->status(), $response->headers());
    } catch (\Exception $e) {
        return response()->json(['error' => 'Falha no proxy: ' . $e->getMessage()], 500);
    }
});


// Rotas de usuário (exemplo)
Route::middleware('auth:sanctum')->get('/users', [\App\Http\Controllers\UserController::class, 'index']);
Route::middleware('auth:sanctum')->get('/users/{id}', [\App\Http\Controllers\UserController::class, 'show']);
Route::middleware('auth:sanctum')->put('/users/{id}', [\App\Http\Controllers\UserController::class, 'update']);
Route::middleware('auth:sanctum')->post('/users', [\App\Http\Controllers\UserController::class, 'store']);
Route::middleware('auth:sanctum')->delete('/users/{id}', [\App\Http\Controllers\UserController::class, 'destroy']);

// Endpoint de teste
Route::get('/health', fn() => ['status' => 'ok']);

Route::middleware('auth:sanctum')->group(function () {

    // Vídeos
    Route::get('/videos', [VideoController::class, 'index']);          // Todos os vídeos
    Route::get('/videos/user', [VideoController::class, 'userVideos']); // Vídeos do usuário logado
    Route::get('/videos/{id}', [VideoController::class, 'show']);      // Mostrar vídeo
    Route::post('/videos', [VideoController::class, 'store']);         // Criar vídeo
    Route::put('/videos/{id}', [VideoController::class, 'update']);    // Atualizar
    Route::delete('/videos/{id}', [VideoController::class, 'destroy']); // Deletar
    Route::post('/videos/{id}/watched', [VideoController::class, 'markWatched']); // Marcar assistido

    // Streaming proxy
    Route::get('/video/{filename}', [VideoController::class, 'stream']);
});
