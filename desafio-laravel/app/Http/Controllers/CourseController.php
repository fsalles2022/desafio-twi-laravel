<?php

namespace App\Http\Controllers;

use App\Models\Course;
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

    public function videos(Course $course)
    {
        $course->load('videos');

        return response()->json([
            'course' => $course,
            'videos' => $course->videos
        ]);
    }

    public function index()
    {
        $user = Auth::user();
        return response()->json($this->repo->allForUser($user->id));
    }

    public function show($id)
    {
        return response()->json($this->repo->find($id));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'         => 'required|string|max:255',
            'description'   => 'nullable|string',
            'course_image'  => 'nullable|string',
            'status'        => 'in:active,inactive',
            'user_id'       => 'nullable|integer|exists:users,id',
        ]);

        $data['slug'] = Str::slug($request->title);
        $data['user_id'] = $data['user_id'] ?? Auth::id();

        $course = $this->repo->create($data);

        return response()->json($course, 201);
    }

    public function update(Request $request, Course $course)
    {
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

    public function destroy(Course $course)
    {
        $this->repo->delete($course);

        return response()->json(['message' => 'Course deleted']);
    }

    /**
     * Matricular estudante em um curso
     */
    public function enroll(Request $request, $courseId)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $course = Course::findOrFail($courseId);

        // Evitar duplicidade
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
