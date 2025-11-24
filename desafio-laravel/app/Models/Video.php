<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'title',
        'description',
        'filename',
    ];

    // Relação com o curso
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    // Usuários que assistiram este vídeo
    public function users()
    {
        return $this->belongsToMany(User::class, 'video_watched')
                    ->withPivot('watched')
                    ->withTimestamps();
    }

    // Accessor para URL do arquivo de vídeo
    public function getUrlAttribute()
    {
        return $this->filename ? asset('storage/videos/' . $this->filename) : null;
    }

    // Mime type do vídeo (útil para frontend)
    public function getMimeTypeAttribute()
    {
        $ext = strtolower(pathinfo($this->filename, PATHINFO_EXTENSION));
        if (in_array($ext, ['mp4', 'mov', 'avi'])) return 'video/mp4';
        if ($ext === 'mp3') return 'audio/mp3';
        return 'application/octet-stream';
    }
}
