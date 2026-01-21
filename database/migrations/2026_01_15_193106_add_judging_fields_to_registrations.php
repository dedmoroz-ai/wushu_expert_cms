<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            // Порядковый номер выступления (1, 2, 3...)
            if (!Schema::hasColumn('registrations', 'sort_order')) {
                $table->integer('sort_order')->nullable();
            }

            // Статус: 0-Ожидает, 1-На ковре, 2-Оценка готова, 3-Завершен
            if (!Schema::hasColumn('registrations', 'status')) {
                $table->tinyInteger('status')->default(0); 
            }

            // Итоговая оценка (например 9.45)
            if (!Schema::hasColumn('registrations', 'score')) {
                $table->decimal('score', 5, 2)->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->dropColumn(['sort_order', 'status', 'score']);
        });
    }
};
