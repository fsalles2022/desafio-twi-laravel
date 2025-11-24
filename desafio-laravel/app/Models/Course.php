<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;
    protected $table = 'courses';

    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'course_image', // <- adicionado
        'description',
        'status',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function videos()
    {
        return $this->hasMany(Video::class);
    }

    public function students()
    {
        return $this->belongsToMany(User::class, 'course_user')
            ->withPivot('progress', 'completed_at')
            ->withTimestamps();
    }
}
