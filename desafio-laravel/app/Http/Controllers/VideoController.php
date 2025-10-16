<?php

namespace App\Http\Controllers;

use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Response;

class VideoController  extends Controller
{
    // Listar todos os vídeos
    public function index()
    {
        return Video::all()->map(function($video) {
            $video->url = url("/api/video/{$video->filename}");
            return $video;
        });
    }

    // Listar vídeos de um usuário logado
    public function userVideos()
    {
        $user = Auth::user();
        return $user->videos->map(function($video) {
            $video->url = url("/api/video/{$video->filename}");
            return $video;
        });
    }

    // Mostrar um vídeo específico
    public function show($id)
    {
        $video = Video::findOrFail($id);
        $video->url = url("/api/video/{$video->filename}");
        return $video;
    }

    // Criar um vídeo
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'filename' => 'required|string',
        ]);

        return Video::create($request->only(['title', 'filename']));
    }

    // Atualizar vídeo
    public function update(Request $request, $id)
    {
        $video = Video::findOrFail($id);
        $video->update($request->only(['title', 'filename']));
        return $video;
    }

    // Apagar vídeo
    public function destroy($id)
    {
        $video = Video::findOrFail($id);
        $video->delete();
        return response()->json(['message' => 'Vídeo deletado']);
    }

    // Marcar vídeo como assistido pelo usuário logado
    public function markWatched($id)
    {
        $user = Auth::user();
        $user->videos()->syncWithoutDetaching([$id => ['watched' => true]]);
        return response()->json(['message' => 'Vídeo marcado como assistido']);
    }

    // Proxy para streaming Node.js
    public function stream($filename)
    {
        $nodeUrl = "http://localhost:4000/stream/{$filename}";

        try {
            $response = Http::withHeaders([
                'Range' => request()->header('Range'),
            ])->get($nodeUrl);

            return Response::make($response->body(), $response->status(), $response->headers());
        } catch (\Exception $e) {
            return response()->json(['error' => 'Falha no proxy: ' . $e->getMessage()], 500);
        }
    }
}
