<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('skill_assessment_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('skill_assessment_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('skor')->default(0); // 0-100
            $table->unsignedSmallInteger('total_soal')->default(0);
            $table->unsignedSmallInteger('benar')->default(0);
            $table->unsignedSmallInteger('salah')->default(0);
            $table->json('detail')->nullable(); // jawaban per soal
            $table->string('status')->default('gagal'); // lulus | gagal
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('skill_assessment_results');
    }
};
