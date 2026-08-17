<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('skill_assessments', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->string('slug')->unique();
            $table->string('kategori')->default('umum');
            $table->text('deskripsi')->nullable();
            $table->unsignedSmallInteger('durasi_menit')->default(10);
            $table->string('status')->default('aktif'); // aktif | draft
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('skill_assessments');
    }
};
