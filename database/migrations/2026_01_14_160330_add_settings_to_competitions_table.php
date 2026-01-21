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
        Schema::table('competitions', function (Blueprint $table) {
            // Связь с Федерацией
            $table->foreignId('federation_id')->nullable()->constrained()->nullOnDelete();
            
            // Лимиты и настройки
            $table->integer('max_events')->default(2)->comment('Макс. видов на 1 человека');
            $table->boolean('has_duilian')->default(false)->comment('Есть ли Дуйлянь?');
        });
    }

    public function down(): void
    {
        Schema::table('competitions', function (Blueprint $table) {
            $table->dropForeign(['federation_id']);
            $table->dropColumn(['federation_id', 'max_events', 'has_duilian']);
        });
    }
};
