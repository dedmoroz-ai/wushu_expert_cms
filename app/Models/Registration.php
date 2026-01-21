<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Registration extends Model
{
    use HasFactory;

    // Разрешаем запись во все поля (удобно для Filament)
    protected $guarded = [];

    protected $casts = [
        'events' => 'array', // Для хранения JSON (если используется)
        'final_score' => 'decimal:2', // Чтобы оценка всегда была числом (9.50)
    ];

    // --- ОСНОВНЫЕ СВЯЗИ ---

    public function competition()
    {
        return $this->belongsTo(Competition::class);
    }

    public function athlete()
    {
        return $this->belongsTo(Athlete::class);
    }

    public function partner()
    {
        return $this->belongsTo(Athlete::class, 'partner_id');
    }

    // --- СВЯЗИ ДЛЯ СОРТИРОВКИ (ОБЯЗАТЕЛЬНЫ) ---
    // Именно по ним работает твой алгоритм "Вид -> Группа"

    public function style()
    {
        return $this->belongsTo(Style::class);
    }

    public function ageGroup()
    {
        return $this->belongsTo(AgeGroup::class);
    }

    // --- СВЯЗЬ ДЛЯ СУДЕЙСТВА ---
    
    /**
     * Связь с оценками судей.
     * Используем модель Score, которую мы создали ранее.
     */
    public function scores()
    {
        return $this->hasMany(Score::class);
    }
}
