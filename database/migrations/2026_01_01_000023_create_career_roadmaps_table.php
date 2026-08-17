<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('career_roadmaps', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->string('slug')->unique();
            $table->string('bidang'); // Teknologi, Desain, Marketing, Keuangan, dsb
            $table->string('ikon')->default('bi-code-slash');
            $table->text('deskripsi')->nullable();
            $table->json('tahapan'); // [{judul, deskripsi, durasi, skills[]}]
            $table->unsignedInteger('gaji_min')->nullable();
            $table->unsignedInteger('gaji_max')->nullable();
            $table->text('prospek')->nullable();
            $table->string('status')->default('aktif'); // aktif | draft
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('career_roadmaps');
    }
};
