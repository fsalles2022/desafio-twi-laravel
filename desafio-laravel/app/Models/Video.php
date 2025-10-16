<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use HasFactory;

    // Campos que podem ser preenchidos via create/update
    protected $fillable = [
        'title',
        'filename',
    ];

    /**
     * Relacionamento com usuários (many-to-many)
     * Pivot table: user_video
     * Pivot field: watched
     */
    public function users()
    {
        return $this->belongsToMany(User::class)
                    ->withPivot('watched')
                    ->withTimestamps();
    }

    /**
     * Retorna a URL completa do vídeo via Laravel
     */
    public function getUrlAttribute()
    {
        return url("/api/video/{$this->filename}");
    }
}
