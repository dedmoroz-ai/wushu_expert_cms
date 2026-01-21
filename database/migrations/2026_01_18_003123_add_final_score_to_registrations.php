<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            // Итоговая оценка (если нет)
            if (!Schema::hasColumn('registrations', 'final_score')) {
                $table->decimal('final_score', 8, 3)->nullable()->after('id');
            }
            // Флаг "Завершен" (если нет)
            if (!Schema::hasColumn('registrations', 'is_completed')) {
                $table->boolean('is_completed')->default(false)->after('final_score');
            }
        });
    }

    public function down(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->dropColumn(['final_score', 'is_completed']);
        });
    }
};
