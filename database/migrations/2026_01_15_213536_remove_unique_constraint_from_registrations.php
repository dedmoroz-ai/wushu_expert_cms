<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            // 1. Удаляем старый запрет (Один спортсмен = одна запись)
            // Имя ключа взято из твоего текста ошибки
            $table->dropUnique('registrations_competition_id_athlete_id_unique');

            // 2. Добавляем новый, правильный запрет:
            // "Нельзя записать спортсмена на ТОТ ЖЕ САМЫЙ стиль в этом турнире дважды".
            // Но на разные стили - можно.
            $table->unique(['competition_id', 'athlete_id', 'style_id'], 'unique_athlete_style_per_competition');
        });
    }

    public function down(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            // Возвращаем как было (для отката)
            $table->dropUnique('unique_athlete_style_per_competition');
            $table->unique(['competition_id', 'athlete_id'], 'registrations_competition_id_athlete_id_unique');
        });
    }
};