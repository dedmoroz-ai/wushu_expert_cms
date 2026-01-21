<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Club extends Model
{
    use HasFactory;
    protected $guarded = [];
    
    // Связь: В клубе много спортсменов
    public function athletes()
    {
        return $this->hasMany(Athlete::class);
    }

    // Связь: В клубе могут быть тренеры (пользователи)
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
