<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Video;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VideoController extends Controller
{
    /**
     * Lista todos os vídeos (teacher/admin)
     */
    public function index()
    {
        $user = Auth::user();

        // ✅ ADMIN → vê tudo
        if ($user->hasRole('admin')) {
            $videos = Video::with('course')->get();
        }

        // ✅ TEACHER → só vídeos dele
        elseif ($user->hasRole('teacher')) {
            $videos = Video::whereHas('users', function ($q) use ($user) {
                $q->where('users.id', $user->id);
            })->with('course')->get();
        } else {
            return response()->json([], 403);
        }

        $videos = $videos->map(function ($v) {
            $v->url = url("/api/video/stream/{$v->filename}");
            return $v;
        });

        return response()->json($videos);
    }


    /**
     * Vídeos do usuário logado (teacher)
     */
    public function userVideos()
    {
        $user = Auth::user();

        $videos = Video::whereHas('users', fn($q) => $q->where('users.id', $user->id))
            ->with('course')
            ->get()
            ->map(function ($v) {
                $v->url = url("/api/video/stream/{$v->filename}");
                return $v;
            });

        return response()->json($videos);
    }

    /**
     * Cadastrar vídeo (teacher/admin)
     */
  public function store(Request $request)
{
    $user = Auth::user();

    $request->validate([
        'title'     => 'required|string|max:255',
        'course_id' => 'required|exists:courses,id',
        'file'      => 'required|file|mimes:mp4,mp3,mov,avi,webp,png,jpg,jpeg|max:10485760',
    ]);

    // 🔐 TEACHER → só pode usar curso próprio
    if ($user->hasRole('teacher')) {
        $ownsCourse = \App\Models\Course::where('id', $request->course_id)
            ->where('user_id', $user->id)
            ->exists();

        if (!$ownsCourse) {
            return response()->json(['error' => 'You do not own this course'], 403);
        }
    }

    $file = $request->file('file');

    if (!$file->isValid()) {
        return response()->json(['error' => 'Invalid upload'], 400);
    }

    $filename = time() . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());

    DB::beginTransaction();

    try {
        $video = Video::create([
            'title'     => $request->title,
            'filename'  => $filename,
            'course_id' => $request->course_id,
        ]);

        // Relaciona vídeo ao usuário (teacher)
        $user->videos()->attach($video->id);

        // Envio para o Node
        $nodeUrl = 'http://localhost:4000/upload';

        $response = Http::attach(
            'file',
            file_get_contents($file->getRealPath()),
            $filename
        )->post($nodeUrl);

        if ($response->failed()) {
            $user->videos()->detach($video->id);
            $video->delete();
            DB::rollBack();

            return response()->json(['error' => 'Failed to upload to video server'], 500);
        }

        DB::commit();

        $video->url = url("/api/video/stream/{$filename}");

        return response()->json($video, 201);

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json(['error' => $e->getMessage()], 500);
    }
}


    /**
     * Atualizar vídeo (teacher/admin)
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $video = Video::findOrFail($id);

        // 🔐 Teacher só pode editar vídeo próprio
        if ($user->hasRole('teacher')) {
            $ownsVideo = $video->users()
                ->where('users.id', $user->id)
                ->exists();

            if (!$ownsVideo) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
        }

        $data = $request->validate([
            'title'     => 'required|string|max:255',
            'course_id' => 'required|exists:courses,id',
        ]);

        $video->update($data);

        return response()->json([
            'message' => 'Vídeo atualizado com sucesso',
            'video'   => $video
        ]);
    }


    /**
     * Deletar vídeo (teacher/admin)
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $video = Video::findOrFail($id);

        // 🔐 Teacher só pode deletar vídeo próprio
        if ($user->hasRole('teacher')) {
            $ownsVideo = $video->users()
                ->where('users.id', $user->id)
                ->exists();

            if (!$ownsVideo) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
        }

        $video->delete();

        return response()->json(['message' => 'Vídeo deletado']);
    }


    /**
     * Streaming via Node
     */
    public function stream($filename)
    {
        $nodeUrl = "http://localhost:4000/stream/{$filename}";

        $response = Http::withHeaders([
            'Range' => request()->header('Range', '')
        ])->get($nodeUrl);

        $headers = [];

        foreach ($response->headers() as $key => $value) {
            $headers[$key] = is_array($value) ? $value[0] : $value;
        }

        return response($response->body(), $response->status())
            ->withHeaders($headers);
    }

    /**
     * Marcar vídeo como assistido (student)
     */
    public function markAsWatched($videoId)
    {
        $user = User::with('roles')->findOrFail(Auth::id());
        $video = Video::findOrFail($videoId);

        // Admin e teacher não precisam marcar
        if ($user->hasAnyRole(['teacher', 'admin'])) {
            return response()->json(['message' => 'Not applicable']);
        }

        // Verifica matrícula
        $isEnrolled = $user->enrolledCourses()
            ->where('course_id', $video->course_id)
            ->exists();

        if (!$isEnrolled) {
            return response()->json(['error' => 'Not enrolled'], 403);
        }

        if (!$user->watchedVideos()->where('video_id', $videoId)->exists()) {
            $user->watchedVideos()->attach($videoId);
        }

        return response()->json([
            'message' => 'Video marked as watched',
            'video_id' => $videoId
        ]);
    }

    /**
     * Desmarcar vídeo como assistido (student)
     */
    public function unmarkAsWatched($videoId)
    {
        $user = User::with('roles')->findOrFail(Auth::id());
        $video = Video::findOrFail($videoId);

        if ($user->hasAnyRole(['teacher', 'admin'])) {
            return response()->json(['message' => 'Not applicable']);
        }

        $isEnrolled = $user->enrolledCourses()
            ->where('course_id', $video->course_id)
            ->exists();

        if (!$isEnrolled) {
            return response()->json(['error' => 'Not enrolled'], 403);
        }

        $user->watchedVideos()->detach($videoId);

        return response()->json([
            'message' => 'Video unmarked as watched',
            'video_id' => $videoId
        ]);
    }
}
