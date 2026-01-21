<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('competition_user', function (Blueprint $table) {
            $table->id();
            
            // Связь с соревнованием
            $table->foreignId('competition_id')->constrained()->cascadeOnDelete();
            
            // Связь с пользователем (судьей)
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            
            // Роль на конкретном турнире
            $table->string('role_on_tournament')->nullable(); 

            $table->timestamps();

            // Защита от дублей
            $table->unique(['competition_id', 'user_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('competition_user');
    }
};
