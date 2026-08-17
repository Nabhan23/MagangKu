<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lamarans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lowongan_id')->constrained()->cascadeOnDelete();
            $table->foreignId('pelamar_profile_id')->constrained()->cascadeOnDelete();
            $table->string('status', 20)->default('menunggu');
            $table->text('catatan')->nullable();
            $table->string('file_pendukung')->nullable();
            $table->unique(['lowongan_id', 'pelamar_profile_id']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lamarans');
    }
};
