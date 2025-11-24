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
        'course_image',
        'description',
        'status',
    ];

    // Adiciona automaticamente o accessor course_image_url ao serializar
    protected $appends = ['course_image_url'];

    // Relação com o professor que criou o curso
    public function teacher()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Todos os vídeos do curso
    public function videos()
    {
        return $this->hasMany(Video::class);
    }

    // Alunos matriculados no curso
    public function students()
    {
        return $this->belongsToMany(User::class, 'course_user')
                    ->withPivot('progress', 'completed_at')
                    ->withTimestamps();
    }

    // Scope para cursos ativos
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    // Retorna URL completa da imagem do curso
    public function getCourseImageUrlAttribute()
    {
        return $this->course_image ? asset('storage/' . $this->course_image) : null;
    }
}
