<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Добавляем Клуб и Роль ТОЛЬКО пользователям (тренерам)
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('coach'); // admin или coach
            $table->foreignId('club_id')->nullable()->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['club_id']);
            $table->dropColumn(['club_id', 'role']);
        });
    }
};
