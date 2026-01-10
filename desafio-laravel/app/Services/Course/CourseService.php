<?php

namespace App\Services\Course;

use App\Models\Course;
use App\Models\User;
use App\Repositories\Contracts\CourseRepositoryInterface;
use Illuminate\Validation\ValidationException;

class CourseService
{
    public function __construct(
        private CourseRepositoryInterface $courses
    ) {}

    public function create(array $data, int $userId): Course
    {
        $user = User::findOrFail($userId);

        if (! $user->hasRole('teacher')) {
            throw new \DomainException('Only teachers can create courses');
        }

        return $this->courses->create([
            'title'       => $data['title'],
            'description' => $data['description'] ?? null,
            'status'      => $data['status'] ?? 'active',
            'user_id'     => $userId,
        ]);
    }


    public function update(int $courseId, array $data): Course
    {
        return $this->courses->update($courseId, $data);
    }

    public function delete(int $courseId): void
    {
        $this->courses->delete($courseId);
    }
}
