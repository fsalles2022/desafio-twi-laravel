<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Video;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class VideoController extends Controller
{
    // Listar todos os vídeos
    public function index()
    {
        return Video::all()->map(function ($video) {
            $video->url = url("/api/video/{$video->filename}");
            return $video;
        });
    }

    // Listar vídeos do usuário logado
    public function userVideos()
    {
        $user = Auth::user();

        return $user->videos->map(function ($video) {
            // Caminho do vídeo dentro do public/storage/videos
            $video->url = asset("storage/videos/{$video->filename}");
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

    // Criar um vídeo: banco + relacionamento + Node
    public function store(Request $request)
    {
        // Validação
        $request->validate([
            'title' => 'required|string|max:255',
            'file'  => 'required|file|mimes:mp4,mp3,mov,avi,webp,png,jpg,jpeg|max:51200', // max 50MB
        ]);

        $file = $request->file('file');
        $filename = $file->getClientOriginalName();

        // 1️⃣ Salvar metadados no banco
        $video = Video::create([
            'title'    => $request->title,
            'filename' => $filename,
        ]);

        // 2️⃣ Relacionar com o usuário logado
        $request->user()->videos()->attach($video->id);

        // 3️⃣ Enviar arquivo para o Node Video Service
        $nodeUrl = 'http://localhost:4000/upload';

        try {
            $response = Http::attach(
                'file',
                file_get_contents($file->getRealPath()),
                $filename
            )->post($nodeUrl);

            if ($response->failed()) {
                // Se falhar, opcional: deletar do banco para manter consistência
                $request->user()->videos()->detach($video->id);
                $video->delete();

                return response()->json([
                    'error' => 'Falha ao enviar o vídeo para o Node Video Service'
                ], 500);
            }
        } catch (\Exception $e) {
            // Se ocorrer erro, opcional: rollback
            $request->user()->videos()->detach($video->id);
            $video->delete();

            return response()->json([
                'error' => 'Erro no Node Video Service: ' . $e->getMessage()
            ], 500);
        }

        // 4️⃣ Retornar vídeo com URL pronta para streaming
        $video->url = url("/api/video/{$video->filename}");

        return response()->json($video, 201);
    }


    // Atualizar vídeo
    public function update(Request $request, $id)
    {
        $video = Video::findOrFail($id);
        $video->update($request->only(['title', 'filename']));
        $video->url = url("/api/video/{$video->filename}");
        return $video;
    }

    // Deletar vídeo
    public function destroy($id)
    {
        $video = Video::findOrFail($id);
        $video->delete();
        return response()->json(['message' => 'Vídeo deletado']);
    }

    // Marcar como assistido
    public function markWatched($id)
    {
        $user = Auth::user();
        // $user->videos()->syncWithoutDetaching([$id => ['watched' => true]]);
        return response()->json(['message' => 'Vídeo marcado como assistido']);
    }

    // Streaming proxy para Node.js
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
