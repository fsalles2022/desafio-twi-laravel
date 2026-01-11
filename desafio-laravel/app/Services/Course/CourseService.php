<?php

namespace App\Services\Course;

use App\Models\Course;
use App\Models\User;
use App\Repositories\Contracts\CourseRepositoryInterface;
use DomainException;



class CourseService
{
    public function __construct(
        private CourseRepositoryInterface $courses
    ) {}



    public function create(array $data, User $user): Course
    {
        if (! $user->hasRole('teacher')) {
            throw new DomainException('Only teachers can create courses');
        }

        return $this->courses->create([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'user_id' => $user->id,
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

    public function getAll(): array
    {
        $courses = $this->courses->getAll();
        return $courses;
    }
}
