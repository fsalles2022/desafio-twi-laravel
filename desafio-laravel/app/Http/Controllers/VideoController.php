<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Video;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VideoController extends Controller
{

    public function index()
    {
        $videos = Video::with('course')->get()->map(function ($v) {
            $v->url = url("/api/video/{$v->filename}");
            return $v;
        });

        return response()->json($videos);
    }

    // Listar vídeos do usuário logado
    public function userVideos()
    {
        $user = Auth::user();

        return $user->videos->map(function ($v) {
            $v->url = url("/api/video/{$v->filename}");
            return $v;
        });
    }

    // Cadastrar vídeo
    public function store(Request $request)
    {
        $request->validate([
            'title'     => 'required|string|max:255',
            'course_id' => 'required|exists:courses,id',
            'file'      => 'required|file|mimes:mp4,mp3,mov,avi,webp,png,jpg,jpeg|max:10485760',
        ]);

        $file = $request->file('file');

        if (!$file->isValid()) {
            return response()->json(['error' => 'Upload inválido'], 400);
        }

        $filename = time() . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());

        DB::beginTransaction();

        try {

            // 1️⃣ Salvar metadados (já com course_id)
            $video = Video::create([
                'title'     => $request->title,
                'filename'  => $filename,
                'course_id' => $request->course_id,
            ]);

            // 2️⃣ Relacionar com usuário logado
            $request->user()->videos()->attach($video->id);

            // 3️⃣ Enviar arquivo para Node Video Service
            $nodeUrl = 'http://localhost:4000/upload';

            $response = Http::attach(
                'file',
                file_get_contents($file->getRealPath()),
                $filename
            )->post($nodeUrl);

            if ($response->failed()) {
                // Rollback geral
                $request->user()->videos()->detach($video->id);
                $video->delete();
                DB::rollBack();

                return response()->json(['error' => 'Falha ao enviar arquivo para Node'], 500);
            }

            DB::commit();

            // Adicionar URL retornável
            $video->url = url("/api/video/{$filename}");

            return response()->json($video, 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Erro: ' . $e->getMessage()], 500);
        }
    }

    // Streaming proxy
    public function stream($filename)
    {
        $nodeUrl = "http://localhost:4000/stream/{$filename}";

        try {
            $response = Http::withHeaders([
                'Range' => request()->header('Range', '')
            ])->get($nodeUrl);

            $headers = [];

            foreach ($response->headers() as $key => $value) {
                $headers[$key] = is_array($value) ? $value[0] : $value;
            }

            return response($response->body(), $response->status())
                ->withHeaders($headers);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Falha no proxy: ' . $e->getMessage()], 500);
        }
    }
}
