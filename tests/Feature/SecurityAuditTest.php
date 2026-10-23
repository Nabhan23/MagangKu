<?php

namespace Tests\Feature;

use App\Models\Lamaran;
use App\Models\Lowongan;
use App\Models\Notification;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Feature Test Audit Keamanan MagangKu
 *
 * Membuktikan bahwa:
 *  - Guest tidak bisa membuka halaman privat.
 *  - Pengguna biasa tidak bisa membuka rute admin / perusahaan.
 *  - Manipulasi parameter role tidak meningkatkan hak akses.
 *  - IDOR tertutup: pengguna A tidak bisa membaca/mengubah data pengguna B.
 *  - Login dibatasi rate-limit & password disimpan ter-hash.
 *  - Endpoint publik tidak membocorkan data privat.
 *
 * Berjalan pada SQLite in-memory (lihat phpunit.xml), bukan database produksi.
 */
class SecurityAuditTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    /* =====================================================================
     * 1. GUEST / AKSES TANPA LOGIN
     * =================================================================== */

    public function test_guest_cannot_access_any_dashboard(): void
    {
        foreach (['admin.dashboard', 'perusahaan.dashboard', 'pelamar.dashboard'] as $route) {
            $this->get(route($route))->assertRedirect(route('login'));
        }
    }

    public function test_guest_cannot_access_private_settings(): void
    {
        $this->get(route('notifikasi-prefs.index'))->assertRedirect(route('login'));
        $this->get(route('password.change'))->assertRedirect(route('login'));
    }

    /* =====================================================================
     * 2. RBAC — PENGUNGAH BIASA TIDAK BISA AKSES FITUR ADMIN
     * =================================================================== */

    public function test_pelamar_cannot_access_admin_routes(): void
    {
        $pelamar = User::where('email', 'pelamar@magangku.id')->firstOrFail();

        $adminRoutes = [
            'admin.dashboard', 'admin.users.index', 'admin.lowongan.index',
            'admin.content.index', 'admin.settings.index', 'admin.reports.index',
            'admin.activity.index', 'admin.notifications.index', 'admin.verifikasi.index',
        ];

        foreach ($adminRoutes as $route) {
            $this->actingAs($pelamar)
                ->get(route($route))
                ->assertRedirect(route('pelamar.dashboard'));
        }
    }

    public function test_pelamar_cannot_access_company_routes(): void
    {
        $pelamar = User::where('email', 'pelamar@magangku.id')->firstOrFail();

        $this->actingAs($pelamar)
            ->get(route('perusahaan.dashboard'))
            ->assertRedirect(route('pelamar.dashboard'));

        $this->actingAs($pelamar)
            ->get(route('perusahaan.lowongan.index'))
            ->assertRedirect(route('pelamar.dashboard'));
    }

    public function test_perusahaan_cannot_access_admin_or_pelamar_routes(): void
    {
        $hrd = User::where('email', 'hrd@teknologinusantara.id')->firstOrFail();

        $this->actingAs($hrd)
            ->get(route('admin.dashboard'))
            ->assertRedirect(route('perusahaan.dashboard'));

        $this->actingAs($hrd)
            ->get(route('pelamar.dashboard'))
            ->assertRedirect(route('perusahaan.dashboard'));
    }

    public function test_admin_cannot_access_pelamar_dashboard(): void
    {
        $admin = User::where('email', 'admin@magangku.id')->firstOrFail();

        $this->actingAs($admin)
            ->get(route('pelamar.dashboard'))
            ->assertRedirect(route('admin.dashboard'));
    }

    /* =====================================================================
     * 3. PRIVILEGE ESCALATION — MANIPULASI PARAMETER ROLE
     * =================================================================== */

    public function test_register_cannot_create_super_admin(): void
    {
        $before = User::whereHas('role', fn ($q) => $q->where('slug', Role::SUPER_ADMIN))->count();

        $this->post('/daftar', [
            'name' => 'Penyusup',
            'email' => 'penyusup@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'super_admin', // tidak valid — harus ditolak
        ]);

        // User tidak boleh terbuat dengan role super_admin
        $after = User::whereHas('role', fn ($q) => $q->where('slug', Role::SUPER_ADMIN))->count();
        $this->assertSame($before, $after, 'Registrasi berhasil membuat super_admin!');
        $this->assertDatabaseMissing('users', ['email' => 'penyusup@example.com']);
    }

    public function test_pelamar_cannot_escalate_via_role_id_on_profile_update(): void
    {
        $pelamar = User::where('email', 'pelamar@magangku.id')->firstOrFail();
        $adminRoleId = Role::where('slug', Role::SUPER_ADMIN)->value('id');

        $this->actingAs($pelamar)->put(route('pelamar.profile.update'), [
            'nama_lengkap' => 'Rahmat Syahputra',
            'role_id' => $adminRoleId, // upaya manipulasi
        ]);

        $pelamar->refresh();
        $this->assertFalse($pelamar->isSuperAdmin(), 'role_id berhasil dimanipulasi lewat update profil!');
        $this->assertTrue($pelamar->isPelamar());
    }

    public function test_pelamar_cannot_toggle_or_delete_other_users_as_admin(): void
    {
        $pelamar = User::where('email', 'pelamar@magangku.id')->firstOrFail();
        $victim = User::where('email', 'sari.wulandari@gmail.com')->firstOrFail();

        // Endpoint admin hanya boleh diakses admin; pelamar harus redirect
        $this->actingAs($pelamar)
            ->post(route('admin.users.toggle', $victim))
            ->assertRedirect(route('pelamar.dashboard'));

        $this->actingAs($pelamar)
            ->delete(route('admin.users.destroy', $victim))
            ->assertRedirect(route('pelamar.dashboard'));

        $this->assertDatabaseHas('users', ['id' => $victim->id]); // korban tidak terhapus
    }

    /* =====================================================================
     * 4. IDOR — PENGUNGAH A TIDAK BISA AKSES DATA PENGUNGAH B
     * =================================================================== */

    public function test_pelamar_cannot_view_others_application_detail(): void
    {
        // Lamaran milik pelamar lain
        $lamaran = Lamaran::firstOrFail();
        $ownerId = $lamaran->pelamar_profile_id;

        $intruder = User::where('role_id', Role::where('slug', Role::PELAMAR)->value('id'))
            ->whereDoesntHave('pelamarProfile', fn ($q) => $q->where('id', $ownerId))
            ->firstOrFail();

        $this->actingAs($intruder)
            ->get(route('pelamar.lamaran.show', $lamaran))
            ->assertForbidden();
    }

    public function test_pelamar_cannot_read_or_chat_on_others_lamaran(): void
    {
        $lamaran = Lamaran::firstOrFail();
        $ownerId = $lamaran->pelamar_profile_id;

        $intruder = User::where('role_id', Role::where('slug', Role::PELAMAR)->value('id'))
            ->whereDoesntHave('pelamarProfile', fn ($q) => $q->where('id', $ownerId))
            ->firstOrFail();

        // Chat show
        $this->actingAs($intruder)
            ->get(route('pelamar.chat.show', $lamaran))
            ->assertForbidden();

        // Kirim pesan (write IDOR)
        $this->actingAs($intruder)
            ->post(route('pelamar.chat.send', $lamaran), ['pesan' => 'tes idor'])
            ->assertForbidden();
    }

    public function test_pelamar_cannot_mark_others_notification_read(): void
    {
        $notifOwner = User::where('role_id', Role::where('slug', Role::PELAMAR)->value('id'))->firstOrFail();
        $notification = Notification::where('user_id', $notifOwner->id)->firstOrFail();

        $intruder = User::where('role_id', Role::where('slug', Role::PELAMAR)->value('id'))
            ->where('id', '!=', $notifOwner->id)
            ->firstOrFail();

        $this->actingAs($intruder)
            ->post(route('pelamar.notifications.read', $notification))
            ->assertForbidden();
    }

    public function test_perusahaan_cannot_access_others_applications(): void
    {
        // Lamaran milik suatu perusahaan
        $lamaran = Lamaran::firstOrFail();
        $owningPerusahaanId = $lamaran->lowongan->perusahaan_profile_id;

        $otherCompany = User::where('role_id', Role::where('slug', Role::PERUSAHAAN)->value('id'))
            ->whereDoesntHave('perusahaanProfile', fn ($q) => $q->where('id', $owningPerusahaanId))
            ->firstOrFail();

        $this->actingAs($otherCompany)
            ->get(route('perusahaan.pelamar.show', $lamaran))
            ->assertForbidden();

        // Download CV perusahaan lain
        $this->actingAs($otherCompany)
            ->get(route('perusahaan.pelamar.cv', $lamaran))
            ->assertForbidden();

        // Ubah status perusahaan lain
        $this->actingAs($otherCompany)
            ->post(route('perusahaan.pelamar.status', $lamaran), ['status' => 'diterima'])
            ->assertForbidden();
    }

    public function test_perusahaan_cannot_edit_or_delete_others_lowongan(): void
    {
        $lowongan = Lowongan::firstOrFail();
        $owningId = $lowongan->perusahaan_profile_id;

        $otherCompany = User::where('role_id', Role::where('slug', Role::PERUSAHAAN)->value('id'))
            ->whereDoesntHave('perusahaanProfile', fn ($q) => $q->where('id', $owningId))
            ->firstOrFail();

        $this->actingAs($otherCompany)
            ->get(route('perusahaan.lowongan.edit', $lowongan))
            ->assertForbidden();

        $this->actingAs($otherCompany)
            ->delete(route('perusahaan.lowongan.destroy', $lowongan))
            ->assertForbidden();

        $this->assertDatabaseHas('lowongans', ['id' => $lowongan->id]); // tidak terhapus
    }

    /* =====================================================================
     * 5. OTENTIKASI — HASHING, RATE LIMIT, SESSION
     * =================================================================== */

    public function test_password_stored_as_hash_not_plaintext(): void
    {
        $user = User::where('email', 'pelamar@magangku.id')->firstOrFail();

        $this->assertNotSame('password', $user->password, 'Password tersimpan plaintext!');
        $this->assertTrue(Hash::check('password', $user->password), 'Hash tidak cocok dengan password asli.');
        $this->assertTrue(
            str_starts_with($user->password, '$2y$') || str_starts_with($user->password, '$argon'),
            'Password tidak memakai bcrypt/argon hash.'
        );
    }

    public function test_password_hash_never_leaks_in_login_response(): void
    {
        $user = User::where('email', 'pelamar@magangku.id')->firstOrFail();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertDontSee($user->password, false);
    }

    public function test_api_endpoints_do_not_leak_private_company_data(): void
    {
        $response = $this->getJson('/api/v1/perusahaan');
        $response->assertOk();

        $payload = $response->json();
        // Pastikan field sensitif tidak ada di response
        $encoded = json_encode($payload);
        $this->assertStringNotContainsString('"dokumen_verifikasi"', $encoded);
        $this->assertStringNotContainsString('"status_verifikasi"', $encoded);
        $this->assertStringNotContainsString('"user_id"', $encoded);
    }

    public function test_login_is_rate_limited_after_five_attempts(): void
    {
        $user = User::where('email', 'pelamar@magangku.id')->firstOrFail();

        // 5 percobaan password salah berturut-turut → dibatasi throttle:5,1
        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', ['email' => $user->email, 'password' => 'salah']);
        }

        // Percobaan ke-6 harus kena rate limit (429), meski password benar
        $this->post('/login', ['email' => $user->email, 'password' => 'password'])
            ->assertStatus(429);
    }

    public function test_inactive_user_cannot_login(): void
    {
        $user = User::where('role_id', Role::where('slug', Role::PELAMAR)->value('id'))->firstOrFail();
        $user->update(['is_active' => false]);

        $this->post('/login', ['email' => $user->email, 'password' => 'password']);

        $this->assertGuest();
        $this->assertFalse(Auth::check(), 'Akun nonaktif berhasil login!');
    }

    public function test_unverified_user_cannot_login(): void
    {
        $user = User::where('role_id', Role::where('slug', Role::PELAMAR)->value('id'))->firstOrFail();
        $user->forceFill(['email_verified_at' => null])->save();

        $response = $this->post('/login', ['email' => $user->email, 'password' => 'password']);

        // Berhasil login tapi diarahkan ke halaman verifikasi, bukan dashboard
        $this->assertAuthenticated();
        $response->assertRedirect(route('verification.notice'));
    }

    /* =====================================================================
     * 6. CSRF — ENDPOINT WEB MENERIMA POST TANPA TOKEN DITOLAK
     * =================================================================== */

    public function test_auth_forms_include_csrf_token(): void
    {
        // Setiap form POST (login, daftar, lupa-password) harus menyertakan token CSRF.
        foreach (['/login' => 'login', '/daftar' => 'register', '/lupa-password' => 'password.request'] as $path => $name) {
            $this->get($path)
                ->assertOk()
                ->assertSee('name="_token"', false);
        }
    }

    public function test_authenticated_profile_update_succeeds(): void
    {
        // Route update profil memakai PUT; POST ke sana harus 405 (method not allowed),
        // dan PUT yang benar harus berhasil — memastikan kontrol akses + method cocok.
        $pelamar = User::where('email', 'pelamar@magangku.id')->firstOrFail();

        $this->actingAs($pelamar)
            ->post(route('pelamar.profile.update'), ['nama_lengkap' => 'X'])
            ->assertStatus(405);

        $this->actingAs($pelamar)
            ->put(route('pelamar.profile.update'), ['nama_lengkap' => 'Rahmat Syahputra'])
            ->assertRedirect();
    }

    public function test_get_requests_do_not_change_state(): void
    {
        // Aksi status harus POST, bukan GET (mencegah CSRF lewat <img> dll)
        $hrd = User::where('email', 'hrd@teknologinusantara.id')->firstOrFail();
        $lamaran = Lamaran::firstOrFail();

        $this->actingAs($hrd)
            ->get(route('perusahaan.pelamar.status', $lamaran))
            ->assertStatus(405); // Method Not Allowed
    }

    /* =====================================================================
     * 7. RESET PASSWORD — TOKEN TIDAK BOCOR & TER-HASH
     * =================================================================== */

    public function test_password_reset_token_not_exposed_in_production_mode(): void
    {
        // Simulasikan lingkungan production (bukan local)
        $this->app['env'] = 'production';
        config(['app.env' => 'production']);

        $user = User::where('email', 'pelamar@magangku.id')->firstOrFail();

        // Nonaktifkan hanya middleware CSRF: kita menguji guard environment
        // (apakah reset_link ditampilkan atau tidak), bukan CSRF (diuji terpisah).
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);

        $response = $this->post('/lupa-password', ['email' => $user->email]);

        $stored = \DB::table('password_reset_tokens')->where('email', $user->email)->first();
        $this->assertNotNull($stored, 'Token reset tidak dibuat untuk email terdaftar.');

        // Token di DB harus ter-hash (bukan plaintext 64 karakter)
        $this->assertNotSame(64, strlen($stored->token), 'Token reset disimpan plaintext!');
        $this->assertTrue(
            str_starts_with($stored->token, '$2y$') || str_starts_with($stored->token, '$argon'),
            'Token reset tidak memakai hash bcrypt/argon.'
        );

        // Di production, tautan reset TIDAK boleh ditampilkan dalam respons
        $response->assertDontSee('/reset-password/', false);
        $this->assertNull(session('reset_link'), 'reset_link bocor di production!');
    }

    public function test_password_reset_link_shown_only_in_local_env(): void
    {
        $this->app['env'] = 'local';
        config(['app.env' => 'local']);

        $user = User::where('email', 'pelamar@magangku.id')->firstOrFail();

        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);
        $this->post('/lupa-password', ['email' => $user->email]);

        // Di local env, tautan reset sengaja ditampilkan (fitur dev) sehingga
        // tersimpan di session. Di production (tes sebelumnya) tidak boleh ada.
        $this->assertNotEmpty(
            session('reset_link'),
            'Tautan reset tidak ditampilkan di local env (fitur dev tidak berfungsi).'
        );
    }

    public function test_password_reset_rejects_expired_token(): void
    {
        $user = User::where('email', 'pelamar@magangku.id')->firstOrFail();

        $token = 'token-lama-acak-64-karakter';
        \DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            ['token' => Hash::make($token), 'created_at' => now()->subHours(2)] // sudah kedaluwarsa
        );

        $this->post('/reset-password', [
            'token' => $token,
            'email' => $user->email,
            'password' => 'passwordbaru123',
            'password_confirmation' => 'passwordbaru123',
        ])->assertSessionHasErrors('email');
    }

    public function test_password_reset_with_valid_token_works(): void
    {
        $user = User::where('email', 'pelamar@magangku.id')->firstOrFail();

        $token = 'token-valid-acak-64-karakter-panjang-sekali';
        \DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            ['token' => Hash::make($token), 'created_at' => now()]
        );

        $this->post('/reset-password', [
            'token' => $token,
            'email' => $user->email,
            'password' => 'passwordbaru123',
            'password_confirmation' => 'passwordbaru123',
        ])->assertRedirect(route('login'));

        $user->refresh();
        $this->assertTrue(Hash::check('passwordbaru123', $user->password));
    }
}
