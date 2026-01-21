<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Добавляем порядок для СТИЛЕЙ
        Schema::table('styles', function (Blueprint $table) {
            if (!Schema::hasColumn('styles', 'sort_order')) {
                $table->integer('sort_order')->default(0); 
            }
        });

        // Добавляем порядок для ВОЗРАСТНЫХ ГРУПП (тебе это тоже понадобится)
        Schema::table('age_groups', function (Blueprint $table) {
            if (!Schema::hasColumn('age_groups', 'sort_order')) {
                $table->integer('sort_order')->default(0); 
            }
        });
    }

    public function down(): void
    {
        Schema::table('styles', function (Blueprint $table) {
            $table->dropColumn('sort_order');
        });

        Schema::table('age_groups', function (Blueprint $table) {
            $table->dropColumn('sort_order');
        });
    }
};
