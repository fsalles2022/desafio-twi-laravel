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
    protected $repo;
    use AuthorizesRequests;

    public function __construct(CourseRepository $repo)
    {
        $this->repo = $repo;
    }

    /**
     * Lista cursos do usuário autenticado
     */
    public function index()
    {
        $user = Auth::user();
        $user = User::with('roles')->find($user->id);

        // Teachers veem cursos criados por eles
        if ($user->hasRole('teacher')) {
            return response()->json($this->repo->allForUser($user->id));
        }

        // Students veem cursos disponíveis e matriculados
        if ($user->hasRole('student')) {
            return response()->json([
                'my_courses'  => $user->studentCourses()->with('teacher')->get(),
                'all_courses' => Course::where('status', 'active')->with('teacher')->get(),
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
        return response()->json($course);
    }

    /**
     * Criar curso (somente TEACHER)
     */
    public function store(Request $request)
    {
        $this->authorize('create', Course::class);

        $data = $request->validate([
            'title'         => 'required|string|max:255',
            'description'   => 'nullable|string',
            'course_image'  => 'nullable|string',
            'status'        => 'in:active,inactive',
        ]);

        $baseSlug = Str::slug($data['title']);
        $slug = $baseSlug;
        $counter = 1;

        // garantir slug único
        while (Course::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        $data['slug'] = $slug;
        $data['user_id'] = auth()->id();

        $course = $this->repo->create($data);

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
            'course_image' => 'nullable|string',
            'status'       => 'in:active,inactive',
        ]);

        if (isset($data['title'])) {
            $data['slug'] = Str::slug($data['title']);
        }

        $course = $this->repo->update($course, $data);

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
        $course->load('videos');

        return response()->json([
            'course' => $course,
            'videos' => $course->videos
        ]);
    }

    /**
     * Matricular aluno em curso
     */
    public function enroll(Request $request, $courseId)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $course = Course::findOrFail($courseId);

        if ($course->students()->where('user_id', $request->user_id)->exists()) {
            return response()->json([
                'message' => 'Aluno já matriculado.'
            ], 409);
        }

        $course->students()->attach($request->user_id, [
            'progress' => 0,
            'completed_at' => null,
        ]);

        return response()->json([
            'message' => 'Aluno matriculado com sucesso.',
            'course'  => $course
        ], 201);
    }
}
