<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Competition extends Model
{
    use HasFactory;

    // Разрешаем заполнять все поля
    protected $guarded = [];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'status_code' => 'integer',
    ];

    // --- СУЩЕСТВУЮЩИЕ СВЯЗИ ---

    // Связь с Федерацией
    public function federation()
    {
        return $this->belongsTo(Federation::class);
    }

    // Связь: У соревнования много заявок
    public function registrations()
    {
        return $this->hasMany(Registration::class);
    }

    /**
     * Кто сейчас на ковре?
     */
    public function currentRegistration()
    {
        return $this->belongsTo(Registration::class, 'current_registration_id');
    }

    // --- НОВЫЕ СВЯЗИ (ДЛЯ СУДЕЙСТВА) ---

    /**
     * Список судей (User), которые назначены именно на ЭТО соревнование.
     * Связь через таблицу 'competition_user'.
     */
    public function judges()
    {
        return $this->belongsToMany(User::class, 'competition_user')
                    ->withPivot('role_on_tournament') // Чтобы знать роль (рефери, боковой)
                    ->withTimestamps();
    }
}
