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


    public function videos()
    {
        return $this->belongsToMany(Video::class)->withPivot('watched')->withTimestamps();
    }

    public function courses()
    {
        return $this->belongsToMany(Course::class, 'course_user')
            ->withPivot('progress', 'completed_at')
            ->withTimestamps();
    }

    public function enrolledCourses()
    {
        return $this->belongsToMany(Course::class, 'course_user');
    }

    public function watchedVideos()
    {
        return $this->belongsToMany(Video::class, 'video_watched')
            ->withTimestamps();
    }
}
