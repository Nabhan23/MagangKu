<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Preferensi notifikasi email per user.
     * Default semua aktif (null berarti aktif semua).
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->json('notification_prefs')->nullable()->after('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('notification_prefs');
        });
    }
};
