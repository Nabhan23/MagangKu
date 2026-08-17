<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('skill_assessment_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('skill_assessment_id')->constrained()->cascadeOnDelete();
            $table->string('pertanyaan');
            $table->string('opsi_a');
            $table->string('opsi_b');
            $table->string('opsi_c');
            $table->string('opsi_d');
            $table->enum('jawaban_benar', ['a', 'b', 'c', 'd']);
            $table->string('pembahasan')->nullable();
            $table->unsignedSmallInteger('urutan')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('skill_assessment_questions');
    }
};
