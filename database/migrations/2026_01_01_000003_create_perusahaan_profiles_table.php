<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('perusahaan_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('nama_perusahaan');
            $table->string('slug')->unique();
            $table->string('logo')->nullable();
            $table->string('industri')->nullable();
            $table->string('lokasi')->nullable();
            $table->text('deskripsi')->nullable();
            $table->string('website')->nullable();
            $table->string('email_kontak')->nullable();
            $table->string('telepon')->nullable();
            $table->string('ukuran_perusahaan', 50)->nullable();
            $table->year('tahun_berdiri')->nullable();
            $table->string('dokumen_verifikasi')->nullable();
            $table->string('status_verifikasi', 20)->default('menunggu');
            $table->text('pesan_verifikasi')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('perusahaan_profiles');
    }
};
