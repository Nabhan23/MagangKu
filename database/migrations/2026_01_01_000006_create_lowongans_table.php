<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lowongans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('perusahaan_profile_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('posisi');
            $table->string('slug')->unique();
            $table->string('jenis', 20)->default('full_time');
            $table->longText('deskripsi');
            $table->text('persyaratan')->nullable();
            $table->decimal('gaji_min', 12, 0)->nullable();
            $table->decimal('gaji_max', 12, 0)->nullable();
            $table->string('lokasi')->nullable();
            $table->boolean('is_remote')->default(false);
            $table->integer('kuota')->default(1);
            $table->date('deadline');
            $table->string('status', 20)->default('aktif');
            $table->integer('views')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lowongans');
    }
};
