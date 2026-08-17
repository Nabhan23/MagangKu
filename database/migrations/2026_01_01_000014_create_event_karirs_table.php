<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_karirs', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->string('slug')->unique();
            $table->string('kategori', 30)->default('webinar');
            $table->longText('deskripsi');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai')->nullable();
            $table->time('waktu')->nullable();
            $table->string('lokasi')->nullable();
            $table->boolean('is_online')->default(false);
            $table->string('link_event')->nullable();
            $table->string('gambar')->nullable();
            $table->integer('kapasitas')->default(100);
            $table->string('penyelenggara')->nullable();
            $table->string('status', 20)->default('aktif');
            $table->integer('views')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_karirs');
    }
};
