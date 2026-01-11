<?php

namespace App\Repositories\Contracts;

use App\Models\Course;

interface CourseRepositoryInterface
{
    public function create(array $data): Course;
    public function update(int $id, array $data): Course;
    public function delete(int $id): bool;
    public function find(int $id): Course;
    public function findBySlug(string $slug): Course;
    public function getAll(): iterable;
    public function forTeacher(int $teacherId): iterable;
}
