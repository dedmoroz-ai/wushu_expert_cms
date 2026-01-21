<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            // Колонка для связи со Стилем (на неё сейчас ругается)
            if (!Schema::hasColumn('registrations', 'style_id')) {
                $table->foreignId('style_id')->nullable()->constrained('styles')->cascadeOnDelete();
            }

            // Колонка для связи с Возрастной группой (нужна для сортировки протокола)
            if (!Schema::hasColumn('registrations', 'age_group_id')) {
                $table->foreignId('age_group_id')->nullable()->constrained('age_groups')->nullOnDelete();
            }

            // Если вдруг нет колонки для текстовой метки возраста (которую ты считаешь)
            if (!Schema::hasColumn('registrations', 'age_group_label')) {
                $table->string('age_group_label')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            // При отмене миграции удаляем колонки, если они есть
            if (Schema::hasColumn('registrations', 'style_id')) {
                $table->dropForeign(['style_id']);
                $table->dropColumn('style_id');
            }
            if (Schema::hasColumn('registrations', 'age_group_id')) {
                $table->dropForeign(['age_group_id']);
                $table->dropColumn('age_group_id');
            }
            if (Schema::hasColumn('registrations', 'age_group_label')) {
                $table->dropColumn('age_group_label');
            }
        });
    }
};
