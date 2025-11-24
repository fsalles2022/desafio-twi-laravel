<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, HasApiTokens, HasRoles, Notifiable;

    protected $fillable = ['name', 'email', 'password', 'image'];
    protected $hidden = ['password', 'remember_token'];

    /**
     * Cursos criados ou matriculados do usuário
     */
    public function courses()
    {
        return $this->belongsToMany(Course::class, 'course_user')
                    ->withPivot('progress', 'completed_at')
                    ->withTimestamps();
    }

    /**
     * Cursos matriculados do aluno
     */
    public function enrolledCourses()
    {
        return $this->belongsToMany(Course::class, 'course_user', 'user_id', 'course_id')
                    ->withPivot('progress', 'completed_at')
                    ->withTimestamps();
    }

    /**
     * Alias para facilitar leitura no controller
     */
    public function studentCourses()
    {
        return $this->enrolledCourses();
    }

    /**
     * Vídeos assistidos pelo usuário
     */
    public function watchedVideos()
    {
        return $this->belongsToMany(Video::class, 'video_watched')
                    ->withPivot('watched')
                    ->withTimestamps();
    }
}
