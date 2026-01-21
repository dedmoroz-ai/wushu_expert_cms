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
        Schema::create('registrations', function (Blueprint $table) {
            $table->id();
            
            // Связи: Кто и Куда
            $table->foreignId('competition_id')->constrained()->cascadeOnDelete();
            $table->foreignId('athlete_id')->constrained()->cascadeOnDelete();
            
            // Список дисциплин (сохраним как JSON: ["changquan", "daoshu"])
            $table->json('events')->nullable(); 
            
            // Возрастная группа на момент заявки (пока оставим nullable, пригодится позже)
            $table->string('age_group_label')->nullable(); 
            
            $table->timestamps();
            
            // Защита: Нельзя дважды заявить одного человека на один турнир
            $table->unique(['competition_id', 'athlete_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registrations');
    }
};
