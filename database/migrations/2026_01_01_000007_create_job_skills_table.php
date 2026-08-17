<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_skills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lowongan_id')->constrained()->cascadeOnDelete();
            $table->foreignId('skill_id')->constrained()->cascadeOnDelete();
            $table->unique(['lowongan_id', 'skill_id']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_skills');
    }
};
