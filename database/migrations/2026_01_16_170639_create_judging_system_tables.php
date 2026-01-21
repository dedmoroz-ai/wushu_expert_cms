<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Таблица Оценок (Создаем, только если её нет)
        if (!Schema::hasTable('scores')) {
            Schema::create('scores', function (Blueprint $table) {
                $table->id();
                $table->foreignId('registration_id')->constrained()->cascadeOnDelete();
                $table->foreignId('judge_id')->constrained('users')->cascadeOnDelete();
                $table->decimal('score', 4, 2);
                $table->timestamps();
                
                $table->unique(['registration_id', 'judge_id']);
            });
        }

        // 2. Обновляем таблицу Соревнований
        Schema::table('competitions', function (Blueprint $table) {
            // Проверяем, есть ли колонки, перед добавлением
            if (!Schema::hasColumn('competitions', 'status_code')) {
                $table->integer('status_code')->default(0); 
            }
            if (!Schema::hasColumn('competitions', 'current_registration_id')) {
                $table->foreignId('current_registration_id')->nullable()->constrained('registrations')->nullOnDelete();
            }
        });

        // 3. Обновляем таблицу Пользователей (ВОТ ЗДЕСЬ БЫЛА ОШИБКА)
        Schema::table('users', function (Blueprint $table) {
            // Проверяем: если колонки 'role' НЕТ, то добавляем её
            if (!Schema::hasColumn('users', 'role')) {
                $table->string('role')->default('trainer'); 
            }
            
            // Проверяем: если колонки 'is_active_judge' НЕТ, то добавляем её
            if (!Schema::hasColumn('users', 'is_active_judge')) {
                $table->boolean('is_active_judge')->default(false);
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scores');
        
        if (Schema::hasTable('competitions')) {
            Schema::table('competitions', function (Blueprint $table) {
                if (Schema::hasColumn('competitions', 'current_registration_id')) {
                    $table->dropForeign(['current_registration_id']);
                    $table->dropColumn('current_registration_id');
                }
                if (Schema::hasColumn('competitions', 'status_code')) {
                    $table->dropColumn('status_code');
                }
            });
        }

        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (Schema::hasColumn('users', 'role')) {
                    // Мы не удаляем role, так как она могла существовать до нас
                    // $table->dropColumn('role'); 
                }
                if (Schema::hasColumn('users', 'is_active_judge')) {
                    $table->dropColumn('is_active_judge');
                }
            });
        }
    }
};
