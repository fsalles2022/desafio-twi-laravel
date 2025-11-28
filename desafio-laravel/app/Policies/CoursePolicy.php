<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Course;

class CoursePolicy
{
    public function create(User $user): bool
    {
        return $user->hasAnyRole(['teacher', 'admin']);
    }

    public function update(User $user, Course $course): bool
    {
        // teacher ou dono do curso
        return $user->id === $course->user_id
            || $user->hasAnyRole(['teacher', 'admin']);
    }

    public function delete(User $user, Course $course): bool
    {
        return $user->id === $course->user_id
            || $user->hasAnyRole(['teacher', 'admin']);
    }
}
