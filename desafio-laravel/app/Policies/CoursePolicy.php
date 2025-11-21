<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Course;

class CoursePolicy
{
    public function create(User $user): bool
    {
        return $user->hasRole('teacher');
    }

    public function update(User $user, Course $course): bool
    {
        return $user->id === $course->user_id || $user->hasRole('teacher');
    }

    public function delete(User $user, Course $course): bool
    {
        return $user->id === $course->user_id || $user->hasRole('teacher');
    }
}
