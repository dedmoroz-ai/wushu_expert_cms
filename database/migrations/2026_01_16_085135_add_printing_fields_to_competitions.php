<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('competitions', function (Blueprint $table) {
            // Логотип и печать
            $table->string('organization_logo')->nullable(); 
            $table->string('organization_stamp')->nullable(); 

            // Главный судья
            $table->string('chief_judge_name')->nullable(); 
            $table->string('chief_judge_signature')->nullable(); 

            // Главный секретарь
            $table->string('chief_secretary_name')->nullable(); 
            $table->string('chief_secretary_signature')->nullable(); 
        });
    }

    public function down(): void
    {
        Schema::table('competitions', function (Blueprint $table) {
            $table->dropColumn([
                'organization_logo', 'organization_stamp',
                'chief_judge_name', 'chief_judge_signature',
                'chief_secretary_name', 'chief_secretary_signature'
            ]);
        });
    }
};
