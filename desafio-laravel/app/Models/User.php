<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, HasApiTokens, Notifiable;

    protected $fillable = ['name', 'email', 'password', 'image'];
    protected $hidden = ['password', 'remember_token'];


    public function videos()
    {
        return $this->belongsToMany(Video::class)->withPivot('watched')->withTimestamps();
    }
}
