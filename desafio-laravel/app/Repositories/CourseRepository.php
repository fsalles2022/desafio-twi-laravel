<?php

namespace App\Repositories;

use App\Models\Course;

class CourseRepository
{
    public function all()
    {
        return Course::with('user')->orderBy('id','desc')->get();
    }

    public function find($id)
    {
        return Course::with('videos')->findOrFail($id);
    }

    public function create(array $data)
    {
        return Course::create($data);
    }

    public function update(Course $Course, array $data)
    {
        $Course->update($data);
        return $Course;
    }

    public function delete(Course $Course)
    {
        return $Course->delete();
    }
}
