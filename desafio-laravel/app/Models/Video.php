<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use HasFactory;

    protected $table = 'videos';

    protected $fillable = [
        'course_id',
        'title',
        'description',
        'filename',
        'duration'
    ];

    /**
     * Curso ao qual o vídeo pertence
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Usuários que assistiram o vídeo
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'video_watched')
                    ->withPivot('watched')
                    ->withTimestamps();
    }
}
