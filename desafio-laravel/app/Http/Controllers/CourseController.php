<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Repositories\CourseRepository;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class CourseController extends Controller
{
    use AuthorizesRequests;

    protected $repo;

    public function __construct(CourseRepository $repo)
    {
        $this->repo = $repo;
    }

    /**
     * Lista cursos do usuário autenticado
     */
    public function index()
    {
        $user = User::with('roles')->find(Auth::id());


        if ($user->hasRole('teacher')) {
            return response()->json([
                'my_courses'  => $this->repo->allForUser($user->id),
                'all_courses' => Course::active()->with('teacher')->get(),
            ]);
        }

        if ($user->hasRole('student')) {
            return response()->json([
                'my_courses'  => $user->studentCourses()->with('teacher')->get(),
                'all_courses' => Course::active()->with('teacher')->get(),
            ]);
        }

        if ($user->hasRole('admin')) {
            return response()->json([
                'my_courses'  => Course::with('teacher')->orderBy('id', 'desc')->get(),
                'all_courses' => Course::active()->with('teacher')->get(),
            ]);
        }




        return response()->json([], 403);
    }

    /**
     * Mostrar 1 curso com vídeos e professor
     */
    public function show(Course $course)
    {
        $course->load(['videos', 'teacher']);
        return response()->json($course);
    }

    /**
     * Criar curso (somente TEACHER)
     */
    public function store(Request $request)
    {
        $this->authorize('create', Course::class);

        $data = $request->validate([
            'title'        => 'required|string|max:255',
            'description'  => 'nullable|string',
            'course_image' => 'nullable|image|max:2048',
            'status'       => 'in:active,inactive',
        ]);

        if ($request->hasFile('course_image')) {
            $data['course_image'] = $request->file('course_image')->store('courses', 'public');
        }

        // Gerar slug único
        $slug = Str::slug($data['title']);
        $counter = 1;
        while (Course::where('slug', $slug)->exists()) {
            $slug = Str::slug($data['title']) . '-' . $counter;
            $counter++;
        }

        $data['slug'] = $slug;
        $data['user_id'] = Auth::id();

        $course = $this->repo->create($data);

        return response()->json($course, 201);
    }

    /**
     * Atualizar curso (somente TEACHER dono)
     */
    public function update(Request $request, Course $course)
    {
        $this->authorize('update', $course);

        $data = $request->validate([
            'title'        => 'sometimes|string|max:255',
            'description'  => 'nullable|string',
            'course_image' => 'nullable|image|max:2048',
            'status'       => 'in:active,inactive',
        ]);

        if ($request->hasFile('course_image')) {
            $data['course_image'] = $request->file('course_image')->store('courses', 'public');
        }

        if (isset($data['title'])) {
            $data['slug'] = Str::slug($data['title']);
        }

        $course = $this->repo->update($course, $data);

        return response()->json($course);
    }

    /**
     * Deletar curso
     */
    public function destroy(Course $course)
    {
        $this->authorize('delete', $course);
        $this->repo->delete($course);

        return response()->json(['message' => 'Course deleted']);
    }


    public function publicList()
    {
        return Course::active()
            ->select('id', 'title', 'description', 'course_image as image', 'slug')
            ->orderBy('id', 'desc')
            ->get();
    }


    /**
     * Listar vídeos do curso
     */
    public function videos(Course $course)
    {
        $user = User::with('roles')->find(Auth::id());

        if ($user->hasRole('teacher') || $user->enrolledCourses->contains($course->id)) {
            return response()->json([
                'course' => $course,
                'videos' => $course->videos
            ]);
        }

        return response()->json(['error' => 'Not enrolled'], 403);
    }

    /**
     * Matricular aluno
     */
    public function selfEnroll(Course $course)
    {
        $user = User::with('roles')->find(Auth::id());

        if ($user->hasRole('teacher')) {
            return response()->json(['message' => 'Teachers cannot enroll'], 403);
        }

        if ($user->enrolledCourses()->where('course_id', $course->id)->exists()) {
            return response()->json(['message' => 'Already enrolled'], 200);
        }

        $user->enrolledCourses()->syncWithoutDetaching([$course->id]);

        return response()->json([
            'message' => 'Enrollment successful',
            'course' => $course->title
        ]);
    }

    /**
     * Vídeos assistidos do usuário
     */
    public function watchedVideos($courseId)
    {
        $user = User::with('roles')->find(Auth::id());

        if (!$user->enrolledCourses()->where('course_id', $courseId)->exists()) {
            return response()->json(['error' => 'Not enrolled'], 403);
        }

        $watched = $user->watchedVideos()
            ->where('course_id', $courseId)
            ->get()
            ->map(fn($v) => [
                'id' => $v->id,
                'title' => $v->title,
                'description' => $v->description,
                'filename' => $v->filename,
                'watched_at' => $v->pivot->created_at,
            ]);

        return response()->json([
            'course_id' => $courseId,
            'watched_count' => $watched->count(),
            'videos' => $watched
        ]);
    }
}
