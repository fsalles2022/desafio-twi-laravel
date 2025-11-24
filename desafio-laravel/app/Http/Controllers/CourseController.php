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

        // Teachers veem cursos criados por eles
        if ($user->hasRole('teacher')) {
            return response()->json([
                'my_courses'  => $this->repo->allForUser($user->id),
                'all_courses' => Course::where('status', 'active')->with('teacher')->get(),
            ]);
        }

        // Students veem cursos disponíveis e matriculados
        if ($user->hasRole('student')) {
            return response()->json([
                'my_courses' => $user->studentCourses()->with('teacher')->get(),
                'all_courses' => Course::where('status', 'active')
                    ->whereDoesntHave('students', function ($q) use ($user) {
                        $q->where('user_id', $user->id);
                    })
                    ->with('teacher')
                    ->get(),
            ]);
        }

        return response()->json([], 403);
    }

    /**
     * Mostrar 1 curso com relacionamento básico
     */
    public function show(Course $course)
    {
        $course->load(['videos', 'teacher']);
        $course->append('course_image_url'); // retorna URL completa da imagem
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
            $path = $request->file('course_image')->store('courses', 'public');
            $data['course_image'] = $path;
        }

        // gerar slug único
        $baseSlug = Str::slug($data['title']);
        $slug = $baseSlug;
        $counter = 1;
        while (Course::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        $data['slug'] = $slug;
        $data['user_id'] = Auth::id();

        $course = $this->repo->create($data);
        $course->append('course_image_url');

        return response()->json($course, 201);
    }

    /**
     * Atualizar curso (somente TEACHER dono do curso)
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
            $path = $request->file('course_image')->store('courses', 'public');
            $data['course_image'] = $path;
        }

        if (isset($data['title'])) {
            $data['slug'] = Str::slug($data['title']);
        }

        $course = $this->repo->update($course, $data);
        $course->append('course_image_url');

        return response()->json($course);
    }

    /**
     * Deletar curso (somente TEACHER dono)
     */
    public function destroy(Course $course)
    {
        $this->authorize('delete', $course);
        $this->repo->delete($course);

        return response()->json(['message' => 'Course deleted']);
    }

    /**
     * Trazer os vídeos do curso
     */
    public function videos(Course $course)
    {
        $user = User::with('roles')->find(Auth::id());

        $course->load('videos');

        if ($user->hasRole('teacher')) {
            return response()->json([
                'course' => $course,
                'videos' => $course->videos
            ]);
        }

        // aluno só vê vídeos se estiver matriculado
        if (! $user->enrolledCourses->contains($course->id)) {
            return response()->json(['error' => 'Not enrolled'], 403);
        }

        return response()->json([
            'course' => $course,
            'videos' => $course->videos
        ]);
    }

    /**
     * Matricular aluno em curso
     */
    public function selfEnroll(Course $course)
    {
        $user = User::with('roles')->find(Auth::id());

        if ($user->hasRole('teacher')) {
            return response()->json(['message' => 'Teachers cannot enroll'], 403);
        }

        if ($user->enrolledCourses()->where('course_id', $course->id)->exists()) {
            return response()->json(['message' => 'You are already enrolled'], 200);
        }

        $user->enrolledCourses()->syncWithoutDetaching([$course->id]);

        return response()->json([
            'message' => 'Enrollment successful',
            'course' => $course->title,
        ]);
    }

    /**
     * Videos assistidos do usuário
     */
    public function watchedVideos($courseId)
    {
        $user = User::with('roles')->find(Auth::id());
        $isEnrolled = $user->enrolledCourses()->where('course_id', $courseId)->exists();
        if (! $isEnrolled) {
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
