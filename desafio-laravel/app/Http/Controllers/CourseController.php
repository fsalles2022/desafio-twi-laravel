<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;
use App\Repositories\CourseRepository;
use Illuminate\Support\Str;

class CourseController extends Controller
{
    protected $repo;

    public function __construct(CourseRepository $repo)
    {
        $this->repo = $repo;
    }

    public function index()
    {
        return response()->json($this->repo->all());
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
        $data['user_id'] = $request->user_id ?? auth()->id();

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
}
