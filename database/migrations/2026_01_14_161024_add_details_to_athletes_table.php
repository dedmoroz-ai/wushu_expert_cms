<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
            public function up(): void
    {
        Schema::table('athletes', function (Blueprint $table) {
            // Добавляем, только если колонки НЕТ в базе
            if (!Schema::hasColumn('athletes', 'gender')) {
                $table->string('gender')->nullable();
            }
            
            if (!Schema::hasColumn('athletes', 'birth_date')) {
                $table->date('birth_date')->nullable();
            }
            
            if (!Schema::hasColumn('athletes', 'rank')) {
                $table->string('rank')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('athletes', function (Blueprint $table) {
            $table->dropColumn(['gender', 'birth_date', 'rank']);
        });
    }
};
