<?php

namespace App\Repositories;

use App\Models\Course;

class CourseRepository
{
    /**
     * Cursos criados pelo usuário (Teacher)
     */
    public function allForUser($userId)
    {
        return Course::where('user_id', $userId)
            ->withCount('videos')
            ->orderBy('id', 'desc')
            ->get();
    }

    /**
     * Cursos que o Teacher criou
     */
    public function getCoursesForTeacher($userId)
    {
        return Course::where('user_id', $userId)
            ->withCount('videos')
            ->orderBy('id', 'desc')
            ->get();
    }

    /**
     * Cursos que o Student está matriculado
     */
    public function getCoursesForStudent($userId)
    {
        return Course::whereHas('students', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->with('videos')
            ->orderBy('id', 'desc')
            ->get();
    }

    /**
     * Encontrar curso específico
     */
    public function find($id)
    {
        return Course::with('user', 'videos')->findOrFail($id);
    }

    /**
     * Criar novo course
     */
    public function create(array $data)
    {
        return Course::create($data);
    }

    /**
     * Atualizar course
     */
    public function update(Course $course, array $data)
    {
        $course->update($data);
        return $course;
    }

    /**
     * Deletar course
     */
    public function delete(Course $course)
    {
        return $course->delete();
    }
}
