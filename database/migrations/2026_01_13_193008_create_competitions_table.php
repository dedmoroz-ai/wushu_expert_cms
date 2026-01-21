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
    Schema::create('competitions', function (Blueprint $table) {
        $table->id();
        $table->string('name'); // Название турнира
        $table->date('start_date'); // Дата начала
        $table->date('end_date')->nullable(); // Дата окончания
        $table->string('city'); // Город проведения
        $table->string('address')->nullable(); // Точный адрес
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('competitions');
    }
};
