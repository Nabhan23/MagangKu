<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pelamar_profiles', function (Blueprint $table) {
            $table->string('ekspektasi_gaji', 100)->nullable()->after('alamat');
        });
    }

    public function down(): void
    {
        Schema::table('pelamar_profiles', function (Blueprint $table) {
            $table->dropColumn('ekspektasi_gaji');
        });
    }
};
