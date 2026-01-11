<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Services\Course\CourseService;
use App\Models\User;
use Tests\Traits\CreatesRoles;

class CourseServiceTest extends TestCase
{
    use RefreshDatabase;
    use CreatesRoles;

    private CourseService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->createRoles(); // cria teacher, student, admin com guard sanctum
        $this->service = app(CourseService::class);
    }

    /** @test */
    public function teacher_pode_criar_curso()
    {
        $teacher = User::factory()->create();
        $teacher->assignRole('teacher');

        $course = $this->service->create(
            ['title' => 'Laravel'],
            $teacher // 👈 USER, não ID
        );

        $this->assertDatabaseHas('courses', [
            'id' => $course->id,
            'user_id' => $teacher->id,
        ]);
    }

    /** @test */
    public function student_nao_pode_criar_curso()
    {
        $student = User::factory()->create();
        $student->assignRole('student');

        $this->expectException(\DomainException::class);

        $this->service->create([
            'title' => 'Curso proibido',
        ], $student); // 👈 USER, não ID
    }
}
