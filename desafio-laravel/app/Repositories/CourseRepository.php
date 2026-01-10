<?php

namespace App\Repositories;

use App\Models\Course;
use App\Repositories\Contracts\CourseRepositoryInterface;
use Illuminate\Support\Str;

class CourseRepository implements CourseRepositoryInterface
{
    public function create(array $data): Course
    {
        return Course::create([
            'title'       => $data['title'],
            'description' => $data['description'] ?? null,
            'user_id'     => $data['user_id'], // 🔥 domínio correto
            'slug'        => Str::slug($data['title']),
            'status'      => $data['status'] ?? 'active',
        ]);
    }

    public function update(int $id, array $data): Course
    {
        $course = Course::findOrFail($id);

        if (isset($data['title'])) {
            $data['slug'] = Str::slug($data['title']);
        }

        $course->update($data);
        return $course;
    }

    public function delete(int $id): bool
    {
        return Course::findOrFail($id)->delete();
    }

    public function find(int $id): Course
    {
        return Course::with(['user', 'videos'])->findOrFail($id);
    }

    public function findBySlug(string $slug): Course
    {
        return Course::where('slug', $slug)
            ->with(['user', 'videos'])
            ->firstOrFail();
    }

    public function all(): iterable
    {
        return Course::with(['user'])
            ->orderByDesc('id')
            ->get();
    }

    public function forTeacher(int $teacherId): iterable
    {
        return Course::where('user_id', $teacherId)
            ->withCount('videos')
            ->orderByDesc('id')
            ->get();
    }
}
