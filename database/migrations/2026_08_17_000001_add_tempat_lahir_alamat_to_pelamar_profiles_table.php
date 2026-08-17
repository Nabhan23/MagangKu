<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pelamar_profiles', function (Blueprint $table) {
            $table->string('tempat_lahir')->nullable()->after('tanggal_lahir');
            $table->text('alamat')->nullable()->after('lokasi');
        });
    }

    public function down(): void
    {
        Schema::table('pelamar_profiles', function (Blueprint $table) {
            $table->dropColumn(['tempat_lahir', 'alamat']);
        });
    }
};
