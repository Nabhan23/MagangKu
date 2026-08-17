<?php

use App\Models\Interview;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('interviews', function (Blueprint $table) {
            $table->string('kode', 12)->nullable()->unique()->after('status');
        });

        // Backfill kode untuk interview yang sudah ada
        foreach (Interview::whereNull('kode')->get() as $interview) {
            $interview->update(['kode' => strtoupper(Str::random(8))]);
        }
    }

    public function down(): void
    {
        Schema::table('interviews', function (Blueprint $table) {
            $table->dropUnique(['kode']);
            $table->dropColumn('kode');
        });
    }
};
