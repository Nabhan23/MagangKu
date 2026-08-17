<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pelamar_profiles', function (Blueprint $table) {
            $table->foreignId('career_roadmap_id')->nullable()->after('views')->constrained('career_roadmaps')->nullOnDelete();
            $table->json('roadmap_progress')->nullable()->after('career_roadmap_id');
        });
    }

    public function down(): void
    {
        Schema::table('pelamar_profiles', function (Blueprint $table) {
            $table->dropConstrainedForeignId('career_roadmap_id');
            $table->dropColumn('roadmap_progress');
        });
    }
};
