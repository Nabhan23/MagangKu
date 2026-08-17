<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pelamar_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('nama_lengkap')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('jenis_kelamin', 20)->nullable();
            $table->string('foto')->nullable();
            $table->string('cv_path')->nullable();
            $table->string('universitas')->nullable();
            $table->string('jurusan')->nullable();
            $table->string('pendidikan_terakhir', 50)->nullable();
            $table->year('tahun_lulus')->nullable();
            $table->string('lokasi')->nullable();
            $table->text('tentang')->nullable();
            $table->string('keahlian')->nullable();
            $table->string('website_portfolio')->nullable();
            $table->string('linkedin')->nullable();
            $table->string('github')->nullable();
            $table->string('badge', 50)->default('pelamar_aktif');
            $table->boolean('is_portfolio_public')->default(true);
            $table->integer('views')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pelamar_profiles');
    }
};
