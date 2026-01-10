<?php

namespace Tests\Unit\Repositories;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Repositories\CourseRepository;
use App\Models\User;

class CourseRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected CourseRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(CourseRepository::class);
    }

    /** @test */
    public function cria_um_curso_com_sucesso()
    {
        $teacher = User::factory()->create();

        $data = [
            'title' => 'Laravel Profissional',
            'description' => 'Curso completo de Laravel 11',
            'user_id' => $teacher->id,
        ];

        $course = $this->repository->create($data);

        $this->assertDatabaseHas('courses', [
            'id' => $course->id,
            'title' => 'Laravel Profissional',
            'user_id' => $teacher->id,
        ]);
    }
}
