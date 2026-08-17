<?php

namespace Tests\Feature;

use App\Mail\CompanyVerificationMail;
use App\Mail\InterviewScheduledMail;
use App\Mail\LamaranStatusMail;
use App\Models\CareerRoadmap;
use App\Models\Lamaran;
use App\Models\Lowongan;
use App\Models\PelamarProfile;
use App\Models\PerusahaanProfile;
use App\Models\Role;
use App\Models\SkillAssessment;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MagangKuTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_public_pages_render(): void
    {
        foreach (['/', '/lowongan', '/perusahaan', '/event', '/artikel', '/tentang', '/kontak', '/faq', '/login', '/daftar'] as $path) {
            $this->get($path)->assertStatus(200);
        }
    }

    public function test_registration_creates_pelamar_account(): void
    {
        $response = $this->post('/daftar', [
            'name' => 'Pengguna Baru',
            'email' => 'baru@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'pelamar',
        ]);

        // User baru diarahkan ke halaman verifikasi email
        $response->assertRedirect(route('verification.notice'));

        $this->assertDatabaseHas('users', ['email' => 'baru@example.com']);
        $this->assertDatabaseHas('pelamar_profiles', [
            'user_id' => User::where('email', 'baru@example.com')->value('id'),
        ]);
    }

    public function test_company_registration_creates_company_account_with_details(): void
    {
        $response = $this->post('/daftar', [
            'role' => 'perusahaan',
            'nama_perusahaan' => 'PT Uji Baru',
            'industri' => 'Fintech',
            'lokasi' => 'Tangerang',
            'telepon' => '021-5551234',
            'website' => 'https://ujibaru.id',
            'ukuran_perusahaan' => '51-200',
            'tahun_berdiri' => 2012,
            'deskripsi' => 'Perusahaan fintech uji.',
            'email' => 'hrd.ujibaru@gmail.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        // User baru diarahkan ke halaman verifikasi email
        $response->assertRedirect(route('verification.notice'));

        $this->assertDatabaseHas('users', [
            'email' => 'hrd.ujibaru@gmail.com',
            'name' => 'PT Uji Baru',
        ]);
        $this->assertDatabaseHas('perusahaan_profiles', [
            'nama_perusahaan' => 'PT Uji Baru',
            'industri' => 'Fintech',
            'lokasi' => 'Tangerang',
            'website' => 'https://ujibaru.id',
            'ukuran_perusahaan' => '51-200',
            'tahun_berdiri' => 2012,
            'status_verifikasi' => 'menunggu',
        ]);
    }

    public function test_company_verification_sends_confirmation_email(): void
    {
        Mail::fake();

        $rolePerusahaan = Role::where('slug', Role::PERUSAHAAN)->first();
        $perusahaanUser = User::create([
            'name' => 'PT Verifikasi Email',
            'email' => 'verif.email@perusahaan.id',
            'password' => 'password',
            'role_id' => $rolePerusahaan->id,
            'email_verified_at' => now(),
        ]);
        $company = PerusahaanProfile::create([
            'user_id' => $perusahaanUser->id,
            'nama_perusahaan' => 'PT Verifikasi Email',
            'slug' => 'pt-verifikasi-email',
            'status_verifikasi' => 'menunggu',
        ]);

        $admin = User::where('email', 'admin@magangku.id')->first();

        $this->actingAs($admin)
            ->post(route('admin.verifikasi.update', $company), [
                'status' => 'verified',
                'pesan' => '',
            ])
            ->assertRedirect();

        Mail::assertSent(CompanyVerificationMail::class, function (CompanyVerificationMail $mail) use ($perusahaanUser, $company) {
            return $mail->hasTo($perusahaanUser->email)
                && $mail->status === 'verified'
                && $mail->perusahaan->id === $company->id;
        });

        $this->assertDatabaseHas('perusahaan_profiles', [
            'id' => $company->id,
            'status_verifikasi' => 'verified',
        ]);
    }

    public function test_company_verification_rejection_sends_email_with_reason(): void
    {
        Mail::fake();

        $rolePerusahaan = Role::where('slug', Role::PERUSAHAAN)->first();
        $perusahaanUser = User::create([
            'name' => 'PT Ditolak Email',
            'email' => 'tolak.email@perusahaan.id',
            'password' => 'password',
            'role_id' => $rolePerusahaan->id,
            'email_verified_at' => now(),
        ]);
        $company = PerusahaanProfile::create([
            'user_id' => $perusahaanUser->id,
            'nama_perusahaan' => 'PT Ditolak Email',
            'slug' => 'pt-ditolak-email',
            'status_verifikasi' => 'menunggu',
        ]);

        $admin = User::where('email', 'admin@magangku.id')->first();

        $this->actingAs($admin)
            ->post(route('admin.verifikasi.update', $company), [
                'status' => 'rejected',
                'pesan' => 'Dokumen tidak lengkap.',
            ])
            ->assertRedirect();

        Mail::assertSent(CompanyVerificationMail::class, function (CompanyVerificationMail $mail) use ($perusahaanUser) {
            return $mail->hasTo($perusahaanUser->email)
                && $mail->status === 'rejected'
                && $mail->pesan === 'Dokumen tidak lengkap.';
        });
    }

    public function test_company_registration_requires_company_fields(): void
    {
        // Role perusahaan tanpa field khusus -> validasi gagal
        $this->post('/daftar', [
            'role' => 'perusahaan',
            'email' => 'hrd.gagal@gmail.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertSessionHasErrors(['nama_perusahaan', 'industri', 'lokasi']);

        $this->assertDatabaseMissing('users', ['email' => 'hrd.gagal@gmail.com']);
    }

    public function test_login_and_role_guard(): void
    {
        $pelamar = User::where('email', 'pelamar@magangku.id')->first();

        $this->actingAs($pelamar)
            ->get(route('pelamar.dashboard'))
            ->assertStatus(200);

        // Pelamar tidak boleh mengakses dashboard admin — diarahkan ke dashboard sendiri
        $this->actingAs($pelamar)
            ->get(route('admin.dashboard'))
            ->assertRedirect(route('pelamar.dashboard'));
    }

    public function test_guest_cannot_access_dashboard(): void
    {
        $this->get(route('pelamar.dashboard'))->assertRedirect(route('login'));
    }

    public function test_job_listing_with_filter(): void
    {
        $this->get(route('jobs.index', ['jenis' => 'magang']))
            ->assertStatus(200)
            ->assertSee('Magang');
    }

    public function test_eligible_pelamar_can_submit_review(): void
    {
        [$company, $pelamarUser] = $this->createReviewScenario('diterima');

        $this->actingAs($pelamarUser)
            ->post(route('companies.review', $company), ['rating' => 5, 'komentar' => 'Pengalaman magang yang luar biasa!'], ['Accept' => 'application/json'])
            ->assertOk()
            ->assertJsonPath('rating', 5);

        $this->assertDatabaseHas('company_reviews', [
            'perusahaan_profile_id' => $company->id,
            'rating' => 5,
            'status' => 'approved',
        ]);
    }

    public function test_pelamar_cannot_review_company_twice(): void
    {
        [$company, $pelamarUser] = $this->createReviewScenario('diterima');

        $this->actingAs($pelamarUser)
            ->post(route('companies.review', $company), ['rating' => 5, 'komentar' => 'Pertama'], ['Accept' => 'application/json'])
            ->assertOk();

        $this->actingAs($pelamarUser)
            ->post(route('companies.review', $company), ['rating' => 4, 'komentar' => 'Kedua'], ['Accept' => 'application/json'])
            ->assertStatus(422);
    }

    public function test_pelamar_without_acceptance_cannot_review(): void
    {
        [$company, $pelamarUser] = $this->createReviewScenario('menunggu');

        $this->actingAs($pelamarUser)
            ->post(route('companies.review', $company), ['rating' => 5, 'komentar' => 'Belum lolos'], ['Accept' => 'application/json'])
            ->assertStatus(422);

        $this->assertDatabaseMissing('company_reviews', ['perusahaan_profile_id' => $company->id]);
    }

    public function test_guest_cannot_review(): void
    {
        [$company] = $this->createReviewScenario('diterima');

        // Permintaan AJAX tanpa sesi -> 401 (middleware auth)
        $this->post(route('companies.review', $company), ['rating' => 5, 'komentar' => 'Tanpa login'], ['Accept' => 'application/json'])
            ->assertStatus(401);

        // Tanpa header JSON -> redirect ke halaman login
        $this->post(route('companies.review', $company), ['rating' => 5, 'komentar' => 'Tanpa login'])
            ->assertRedirect(route('login'));
    }

    public function test_public_roadmap_and_assessment_pages_render(): void
    {
        $this->get(route('roadmaps.index'))->assertStatus(200);
        $this->get(route('assessment.index'))->assertStatus(200);

        $roadmap = CareerRoadmap::where('status', 'aktif')->first();
        $this->get(route('roadmaps.show', $roadmap))->assertStatus(200);
    }

    public function test_guest_redirected_to_login_for_assessment(): void
    {
        $assessment = SkillAssessment::where('status', 'aktif')->first();

        $this->get(route('assessment.show', $assessment))->assertRedirect(route('login'));
    }

    public function test_non_pelamar_cannot_take_assessment(): void
    {
        $assessment = SkillAssessment::where('status', 'aktif')->first();
        $company = User::where('email', 'hrd@teknologinusantara.id')->first();

        $this->actingAs($company)->get(route('assessment.show', $assessment))->assertStatus(403);
        $this->actingAs($company)->post(route('assessment.submit', $assessment))->assertStatus(403);
    }

    public function test_pelamar_can_submit_assessment_and_get_score(): void
    {
        $pelamar = User::where('email', 'pelamar@magangku.id')->first();
        $assessment = SkillAssessment::where('kategori', 'teknologi')->first();

        $jawaban = [];
        foreach ($assessment->questions as $q) {
            $jawaban[$q->id] = $q->jawaban_benar;
        }

        $this->actingAs($pelamar)
            ->post(route('assessment.submit', $assessment), ['jawaban' => $jawaban], ['Accept' => 'application/json'])
            ->assertOk()
            ->assertJsonPath('skor', 100)
            ->assertJsonPath('status', 'lulus');

        $this->assertDatabaseHas('skill_assessment_results', [
            'user_id' => $pelamar->id,
            'skill_assessment_id' => $assessment->id,
            'skor' => 100,
            'status' => 'lulus',
        ]);
    }

    public function test_pelamar_can_follow_roadmap_and_toggle_steps(): void
    {
        $pelamar = User::where('email', 'pelamar@magangku.id')->first();
        $roadmap = CareerRoadmap::where('slug', 'backend-developer')->first();

        $this->actingAs($pelamar)
            ->post(route('roadmaps.start', $roadmap), [], ['Accept' => 'application/json'])
            ->assertOk();

        $this->actingAs($pelamar)
            ->post(route('roadmaps.step', [$roadmap, 0]), [], ['Accept' => 'application/json'])
            ->assertOk()
            ->assertJsonPath('persen', 20)
            ->assertJsonPath('selesai', false);

        $this->assertSame(
            [0],
            $pelamar->fresh()->pelamarProfile->roadmap_progress
        );
    }

    public function test_non_pelamar_cannot_start_roadmap(): void
    {
        $roadmap = CareerRoadmap::where('slug', 'backend-developer')->first();
        $company = User::where('email', 'hrd@teknologinusantara.id')->first();

        $this->actingAs($company)
            ->post(route('roadmaps.start', $roadmap), [], ['Accept' => 'application/json'])
            ->assertRedirect(route('perusahaan.dashboard'));
    }

    public function test_status_change_sends_email_to_pelamar(): void
    {
        Mail::fake();

        [$company, $pelamarUser, $lamaran] = $this->createLamaranScenario('menunggu');

        $this->actingAs($company->user)
            ->post(route('perusahaan.pelamar.status', $lamaran), ['status' => 'diproses'], ['Accept' => 'application/json'])
            ->assertOk();

        Mail::assertSent(LamaranStatusMail::class, function (LamaranStatusMail $mail) use ($pelamarUser, $lamaran) {
            return $mail->hasTo($pelamarUser->email)
                && $mail->lamaran->id === $lamaran->id
                && $mail->oldStatus === 'menunggu';
        });

        $this->assertDatabaseHas('lamarans', ['id' => $lamaran->id, 'status' => 'diproses']);
    }

    public function test_interview_schedule_sends_email_to_pelamar(): void
    {
        Mail::fake();

        [$company, $pelamarUser, $lamaran] = $this->createLamaranScenario('diproses');

        $this->actingAs($company->user)
            ->post(route('perusahaan.pelamar.interview', $lamaran), [
                'tanggal' => now()->addDays(3)->format('Y-m-d'),
                'waktu' => '10:00',
                'tipe' => 'online',
                'link' => 'https://meet.google.com/test-interview',
                'catatan' => 'Siapkan portofolio',
            ], ['Accept' => 'application/json'])
            ->assertOk();

        Mail::assertSent(InterviewScheduledMail::class, function (InterviewScheduledMail $mail) use ($pelamarUser) {
            return $mail->hasTo($pelamarUser->email)
                && $mail->interview->tipe === 'online';
        });
    }

    /**
     * @return array{PerusahaanProfile, User, Lamaran}
     */
    protected function createLamaranScenario(string $lamaranStatus): array
    {
        $rolePerusahaan = Role::where('slug', Role::PERUSAHAAN)->first();
        $rolePelamar = Role::where('slug', Role::PELAMAR)->first();

        $perusahaanUser = User::create([
            'name' => 'PT Email Test',
            'email' => 'email.test@perusahaan.id',
            'password' => 'password',
            'role_id' => $rolePerusahaan->id,
            'email_verified_at' => now(),
        ]);
        $company = PerusahaanProfile::create([
            'user_id' => $perusahaanUser->id,
            'nama_perusahaan' => 'PT Email Test',
            'slug' => 'pt-email-test',
            'status_verifikasi' => 'verified',
        ]);

        $pelamarUser = User::create([
            'name' => 'Pelamar Email',
            'email' => 'pelamar.email@example.com',
            'password' => 'password',
            'role_id' => $rolePelamar->id,
            'email_verified_at' => now(),
        ]);
        $pelamar = PelamarProfile::create([
            'user_id' => $pelamarUser->id,
            'nama_lengkap' => 'Pelamar Email',
        ]);

        $lowongan = Lowongan::create([
            'perusahaan_profile_id' => $company->id,
            'posisi' => 'Staff Email Test',
            'deskripsi' => 'Deskripsi lowongan email test.',
            'deadline' => now()->addDays(14),
            'status' => 'aktif',
        ]);

        $lamaran = Lamaran::create([
            'lowongan_id' => $lowongan->id,
            'pelamar_profile_id' => $pelamar->id,
            'status' => $lamaranStatus,
        ]);

        return [$company, $pelamarUser, $lamaran];
    }

    /**
     * @return array{PerusahaanProfile, User}
     */
    protected function createReviewScenario(string $lamaranStatus): array
    {
        $rolePerusahaan = Role::where('slug', Role::PERUSAHAAN)->first();
        $rolePelamar = Role::where('slug', Role::PELAMAR)->first();

        $perusahaanUser = User::create([
            'name' => 'PT Uji Coba',
            'email' => 'uji@perusahaan.id',
            'password' => 'password',
            'role_id' => $rolePerusahaan->id,
            'email_verified_at' => now(),
        ]);
        $company = PerusahaanProfile::create([
            'user_id' => $perusahaanUser->id,
            'nama_perusahaan' => 'PT Uji Coba',
            'slug' => 'pt-uji-coba',
            'status_verifikasi' => 'verified',
        ]);

        $pelamarUser = User::create([
            'name' => 'Pelamar Uji',
            'email' => 'pelamar.uji@example.com',
            'password' => 'password',
            'role_id' => $rolePelamar->id,
            'email_verified_at' => now(),
        ]);
        $pelamar = PelamarProfile::create([
            'user_id' => $pelamarUser->id,
            'nama_lengkap' => 'Pelamar Uji',
        ]);

        $lowongan = Lowongan::create([
            'perusahaan_profile_id' => $company->id,
            'posisi' => 'Magang Uji Coba',
            'deskripsi' => 'Deskripsi lowongan uji coba.',
            'deadline' => now()->addDays(14),
            'status' => 'aktif',
        ]);

        Lamaran::create([
            'lowongan_id' => $lowongan->id,
            'pelamar_profile_id' => $pelamar->id,
            'status' => $lamaranStatus,
        ]);

        return [$company, $pelamarUser];
    }

    public function test_locale_switch_to_english_translates_landing(): void
    {
        $this->get('/locale/en')->assertRedirect();

        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Sign Up Free');
        $response->assertSee('>Home<', false);
        $response->assertSee('Internship');
        $response->assertDontSee('Daftar Gratis');
    }

    public function test_locale_switch_back_to_indonesian_restores_default(): void
    {
        $this->get('/locale/en')->assertRedirect();
        $this->get('/')->assertSee('Sign Up Free');

        $this->get('/locale/id')->assertRedirect();

        $response = $this->get('/');
        $response->assertSee('Daftar Gratis');
        $response->assertSee('>Beranda<', false);
        $response->assertDontSee('Sign Up Free');
    }

    public function test_admin_report_exports_pdf_excel_csv(): void
    {
        $admin = User::where('email', 'admin@magangku.id')->firstOrFail();

        $this->actingAs($admin)
            ->get('/admin/laporan/export?format=pdf')
            ->assertStatus(200)
            ->assertHeader('Content-Type', 'application/pdf');

        $this->actingAs($admin)
            ->get('/admin/laporan/export?format=excel')
            ->assertStatus(200);

        $this->actingAs($admin)
            ->get('/admin/laporan/export?format=csv')
            ->assertStatus(200)
            ->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
    }

    public function test_certificate_only_for_passed_assessment(): void
    {
        $user = User::where('email', 'pelamar@magangku.id')->firstOrFail();
        $lulus = $user->skillAssessmentResults()->where('status', 'lulus')->firstOrFail();
        $gagal = $user->skillAssessmentResults()->where('status', 'gagal')->firstOrFail();

        $this->actingAs($user)
            ->get(route('assessment.certificate', $lulus))
            ->assertStatus(200)
            ->assertSee($user->name)
            ->assertSee($lulus->no_sertifikat);

        // Hasil gagal tidak boleh punya sertifikat
        $this->actingAs($user)
            ->get(route('assessment.certificate', $gagal))
            ->assertStatus(404);

        // Sertifikat milik orang lain → 403
        $other = User::where('email', 'sari.wulandari@gmail.com')->firstOrFail();
        $this->actingAs($other)
            ->get(route('assessment.certificate', $lulus))
            ->assertStatus(403);
    }

    public function test_seo_sitemap_and_robots(): void
    {
        $this->get('/sitemap.xml')
            ->assertStatus(200)
            ->assertHeader('Content-Type', 'application/xml')
            ->assertSee('<urlset', false);

        $this->get('/robots.txt')
            ->assertStatus(200)
            ->assertSee('Sitemap:');
    }

    public function test_social_login_redirect_requires_config(): void
    {
        // Tanpa kredensial OAuth, redirect harus kembali ke login dengan pesan error
        $this->get('/auth/google/redirect')
            ->assertRedirect(route('login'));
    }

    public function test_chat_pelamar_to_perusahaan_stores_message(): void
    {
        $pelamar = User::where('email', 'pelamar@magangku.id')->firstOrFail();
        $lamaran = $pelamar->pelamarProfile->lamarans()->firstOrFail();

        $this->actingAs($pelamar)
            ->post(route('pelamar.chat.send', $lamaran), ['pesan' => 'Halo, saya tertarik dengan posisi ini!'])
            ->assertStatus(200);

        $this->assertDatabaseHas('messages', [
            'lamaran_id' => $lamaran->id,
            'sender_id' => $pelamar->id,
        ]);
    }

    public function test_jobs_page_shows_recommendations_for_pelamar(): void
    {
        $pelamar = User::where('email', 'pelamar@magangku.id')->firstOrFail();

        $this->actingAs($pelamar)
            ->get(route('jobs.index'))
            ->assertStatus(200)
            ->assertSee('Rekomendasi Untukmu');
    }

    public function test_company_can_send_feedback_to_pelamar(): void
    {
        $hrd = User::where('email', 'hrd@teknologinusantara.id')->firstOrFail();
        $lamaran = $hrd->perusahaanProfile->lowongans()->first()->lamarans()->firstOrFail();

        $this->actingAs($hrd)
            ->post(route('perusahaan.pelamar.feedback', $lamaran), [
                'rating' => 4,
                'feedback' => 'Komunikasi Anda sangat baik, pertahankan!',
            ], ['Accept' => 'application/json'])
            ->assertOk()
            ->assertJsonPath('message', 'Feedback berhasil dikirim ke pelamar.');

        $this->assertDatabaseHas('company_feedbacks', [
            'lamaran_id' => $lamaran->id,
            'rating' => 4,
            'feedback' => 'Komunikasi Anda sangat baik, pertahankan!',
        ]);

        // Pelamar melihat feedback di halaman lamaran
        $this->actingAs($lamaran->pelamar->user)
            ->get(route('pelamar.lamaran.show', $lamaran))
            ->assertStatus(200)
            ->assertSee('Feedback dari Perusahaan')
            ->assertSee('Komunikasi Anda sangat baik, pertahankan!');
    }

    public function test_feedback_validates_rating(): void
    {
        $hrd = User::where('email', 'hrd@teknologinusantara.id')->firstOrFail();
        $lamaran = $hrd->perusahaanProfile->lowongans()->first()->lamarans()->firstOrFail();

        $this->actingAs($hrd)
            ->post(route('perusahaan.pelamar.feedback', $lamaran), [
                'rating' => 9,
            ], ['Accept' => 'application/json'])
            ->assertStatus(422);

        $this->assertDatabaseMissing('company_feedbacks', ['lamaran_id' => $lamaran->id]);
    }
}
