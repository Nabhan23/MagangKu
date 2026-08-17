<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->string('slug')->unique();
            $table->string('kategori', 50)->default('dunia_kerja');
            $table->string('ringkasan')->nullable();
            $table->longText('konten');
            $table->string('gambar')->nullable();
            $table->string('penulis')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->string('status', 20)->default('published');
            $table->integer('views')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
