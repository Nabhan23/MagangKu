<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('interviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lamaran_id')->constrained()->cascadeOnDelete();
            $table->date('tanggal');
            $table->time('waktu');
            $table->string('tipe', 20)->default('online');
            $table->string('link')->nullable();
            $table->string('lokasi')->nullable();
            $table->text('catatan')->nullable();
            $table->string('status', 20)->default('dijadwalkan');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('interviews');
    }
};
