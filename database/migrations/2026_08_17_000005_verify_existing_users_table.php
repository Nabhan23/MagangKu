<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * User yang terdaftar SEBELUM fitur verifikasi email aktif dianggap sudah
     * terverifikasi (tidak perlu verifikasi ulang).
     */
    public function up(): void
    {
        DB::table('users')
            ->whereNull('email_verified_at')
            ->update(['email_verified_at' => now()]);
    }

    public function down(): void
    {
        // Tidak ada rollback yang aman — biarkan nilai tetap.
    }
};
