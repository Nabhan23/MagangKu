<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lamarans', function (Blueprint $table) {
            $table->string('offer_letter_path')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('lamarans', function (Blueprint $table) {
            $table->dropColumn('offer_letter_path');
        });
    }
};
