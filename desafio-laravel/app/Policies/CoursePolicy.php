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
        if ($user->hasRole('admin')) {
            return true;
        }

        return $user->hasRole('teacher') && $user->id === $course->user_id;
    }


    public function delete(User $user, Course $course): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        return $user->hasRole('teacher') && $user->id === $course->user_id;
    }

    
}
