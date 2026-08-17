<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('perusahaan_profile_id')->constrained()->cascadeOnDelete();
            $table->foreignId('pelamar_profile_id')->constrained()->cascadeOnDelete();
            $table->tinyInteger('rating')->default(5);
            $table->text('komentar')->nullable();
            $table->string('status', 20)->default('approved');
            $table->unique(['perusahaan_profile_id', 'pelamar_profile_id']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_reviews');
    }
};
