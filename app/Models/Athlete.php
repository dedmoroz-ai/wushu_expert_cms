<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Athlete extends Model
{
    use HasFactory;

    // Какие поля можно заполнять
    protected $fillable = [
        'club_id',
        'name',
        'gender',
        'birth_date',
        'rank',
    ];

    // САМОЕ ВАЖНОЕ:
    // Эта настройка превращает строку из базы в Дату
    protected $casts = [
        'birth_date' => 'date',
    ];

    // Связь: Спортсмен принадлежит Клубу
    public function club()
    {
        return $this->belongsTo(Club::class);
    }
}
