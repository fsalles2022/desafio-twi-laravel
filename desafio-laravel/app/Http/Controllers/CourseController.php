<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Repositories\CourseRepository;

class CourseController extends Controller
{
    protected $repo;

    public function __construct(CourseRepository $repo)
    {
        $this->repo = $repo;
    }

    /**
     * Retorna vídeos do curso
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
     * Listagem de cursos conforme ROLE
     * teacher → cursos criados
     * student → cursos matriculados
     */
    public function index()
    {
        $user = Auth::user();
        $user = User::find($user->id);

        if ($user->hasRole('teacher')) {
            $courses = $this->repo->getCoursesForTeacher($user->id);
        } else {
            $courses = $this->repo->getCoursesForStudent($user->id);
        }

        return response()->json($courses);
    }

    /**
     * Mostrar curso
     */
    public function show($id)
    {
        return response()->json($this->repo->find($id));
    }

    /**
     * Criar curso (somente teacher)
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $user = User::find($user->id);

        if (!$user->hasRole('teacher')) {
            return response()->json([
                'message' => 'Apenas professores podem criar cursos.'
            ], 403);
        }

        $data = $request->validate([
            'title'         => 'required|string|max:255',
            'description'   => 'nullable|string',
            'course_image'  => 'nullable|string',
            'status'        => 'in:active,inactive',
        ]);

        $data['slug'] = Str::slug($data['title']);
        $data['user_id'] = $user->id;

        $course = $this->repo->create($data);

        return response()->json($course, 201);
    }

    /**
     * Atualizar curso
     */
    public function update(Request $request, Course $course)
    {
        $user = Auth::user();

        if ($course->user_id !== $user->id) {
            return response()->json([
                'message' => 'Você não tem permissão para editar este curso.'
            ], 403);
        }

        $data = $request->validate([
            'title'        => 'sometimes|string|max:255',
            'description'  => 'nullable|string',
            'course_image' => 'nullable|string',
            'status'       => 'in:active,inactive',
        ]);

        if (isset($data['title'])) {
            $data['slug'] = Str::slug($data['title']);
        }

        $updated = $this->repo->update($course, $data);

        return response()->json($updated);
    }

    /**
     * Deletar curso
     */
    public function destroy(Course $course)
    {
        $user = Auth::user();

        if ($course->user_id !== $user->id) {
            return response()->json([
                'message' => 'Você não tem permissão para excluir este curso.'
            ], 403);
        }

        $this->repo->delete($course);

        return response()->json(['message' => 'Course deleted']);
    }

    /**
     * Matricular estudante
     */
    public function enroll(Request $request, $courseId)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $course = Course::findOrFail($courseId);

        if ($course->students()->where('user_id', $request->user_id)->exists()) {
            return response()->json([
                'message' => 'O aluno já está matriculado neste curso.'
            ], 409);
        }

        $course->students()->attach($request->user_id, [
            'progress'      => 0,
            'completed_at'  => null,
        ]);

        return response()->json([
            'message' => 'Aluno matriculado com sucesso.',
            'course'  => $course
        ], 201);
    }
}
