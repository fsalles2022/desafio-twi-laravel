<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Repositories\CourseRepository;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;


use App\Services\Course\CourseService;

class CourseController extends Controller
{
    use AuthorizesRequests;
    public function __construct(
        private CourseService $courses
    ) {}

    public function store(Request $request)
    {
        $this->authorize('create', Course::class);

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'in:active,inactive'
        ]);

        return $this->courses->create($data, Auth::user());

        $data['user_id'] = Auth::id(); // 👈 contexto do usuário autenticado

        $course = $this->courses->create($data);

        return response()->json($course, 201);
    }



    public function update(Request $request, Course $course)
    {
        $this->authorize('update', $course);

        $data = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'status' => 'in:active,inactive'
        ]);

        return $this->courses->update($course->id, $data);
    }

    public function destroy(Course $course)
    {
        $this->authorize('delete', $course);

        $this->courses->delete($course->id);

        return response()->json(['message' => 'Deleted']);
    }

    public function index()
    {
        $this->authorize('getAll', Course::class);

        return $this->courses->getAll();
    }
}
