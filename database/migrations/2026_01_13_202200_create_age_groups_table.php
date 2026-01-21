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
    Schema::create('age_groups', function (Blueprint $table) {
        $table->id();
        $table->string('name'); // Название (Юниоры)
        $table->string('gender'); // Пол (male/female)
        $table->integer('min_age'); // От
        $table->integer('max_age'); // До
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('age_groups');
    }
};
