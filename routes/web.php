<?php

use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\ContentController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\LowonganController as AdminLowonganController;
use App\Http\Controllers\Admin\NotificationController as AdminNotificationController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SettingController as AdminSettingController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\VerificationController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\AssessmentController;
use App\Http\Controllers\RoadmapController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\Auth\VerificationController as AuthVerificationController;
use App\Http\Controllers\Candidate\ApplicationController;
use App\Http\Controllers\Candidate\ChatController as CandidateChatController;
use App\Http\Controllers\Candidate\DashboardController as CandidateDashboardController;
use App\Http\Controllers\Candidate\NotificationController as CandidateNotificationController;
use App\Http\Controllers\Candidate\ProfileController as CandidateProfileController;
use App\Http\Controllers\Candidate\SavedJobController;
use App\Http\Controllers\Company\ChatController as CompanyChatController;
use App\Http\Controllers\Company\InterviewController;
use App\Http\Controllers\Company\DashboardController as CompanyDashboardController;
use App\Http\Controllers\Company\LowonganController as CompanyLowonganController;
use App\Http\Controllers\Company\PelamarController;
use App\Http\Controllers\Company\ProfileController as CompanyProfileController;
use App\Http\Controllers\CompanyEventController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\NotificationPrefController;
use App\Http\Controllers\OfferLetterController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PublicCompanyController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SeoController;
use Illuminate\Support\Facades\Route;

/* ================= PUBLIC ================= */
Route::get('/', [LandingController::class, 'index'])->name('home');

/* SEO: robots.txt & sitemap.xml */
Route::get('/robots.txt', [SeoController::class, 'robots']);
Route::get('/sitemap.xml', [SeoController::class, 'sitemap']);

/* Ganti bahasa (ID/EN) */
Route::get('/locale/{lang}', function (string $lang) {
    if (in_array($lang, ['id', 'en'], true)) {
        session(['locale' => $lang]);
        cookie()->queue('locale', $lang, 60 * 24 * 365);
    }

    return redirect()->back();
})->name('locale.switch');

Route::prefix('lowongan')->name('jobs.')->group(function () {
    Route::get('/', [JobController::class, 'index'])->name('index');
    Route::get('/{lowongan:slug}', [JobController::class, 'show'])->name('show');
});

/* Dashboard perusahaan harus dideklarasikan sebelum route publik {perusahaan:slug} */
Route::prefix('perusahaan/dashboard')->name('perusahaan.')->middleware(['auth', 'verified', 'role:perusahaan'])->group(function () {
    Route::get('/', [CompanyDashboardController::class, 'index'])->name('dashboard');
    Route::get('/profil', [CompanyProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profil', [CompanyProfileController::class, 'update'])->name('profile.update');
    Route::post('/profil/logo', [CompanyProfileController::class, 'uploadLogo'])->name('profile.uploadLogo');
    Route::post('/profil/dokumen', [CompanyProfileController::class, 'uploadDokumen'])->name('profile.uploadDokumen');
    Route::get('/profil/dokumen', [CompanyProfileController::class, 'downloadDokumen'])->name('profile.dokumen');
    Route::resource('lowongan', CompanyLowonganController::class)->names('lowongan');
    Route::get('/pelamar', [PelamarController::class, 'index'])->name('pelamar.index');
    Route::post('/pelamar/bulk-status', [PelamarController::class, 'bulkStatus'])->name('pelamar.bulkStatus');
    Route::get('/pelamar/{lamaran}', [PelamarController::class, 'show'])->name('pelamar.show');
    Route::post('/pelamar/{lamaran}/offer-letter', [OfferLetterController::class, 'generate'])->name('pelamar.offerLetter');
    Route::get('/pelamar/{lamaran}/cv', [PelamarController::class, 'downloadCv'])->name('pelamar.cv');
    Route::post('/pelamar/{lamaran}/status', [PelamarController::class, 'updateStatus'])->name('pelamar.status');
    Route::post('/pelamar/{lamaran}/interview', [PelamarController::class, 'scheduleInterview'])->name('pelamar.interview');
    Route::post('/pelamar/{lamaran}/feedback', [PelamarController::class, 'giveFeedback'])->name('pelamar.feedback');
    Route::get('/pelamar/export/{lowongan}', [PelamarController::class, 'export'])->name('pelamar.export');
    /* Kalender interview */
    Route::get('/interview', [InterviewController::class, 'calendar'])->name('interview.calendar');
    /* Chat */
    Route::get('/chat', [CompanyChatController::class, 'index'])->name('chat.index');
    Route::get('/chat/{lamaran}', [CompanyChatController::class, 'show'])->name('chat.show');
    Route::post('/chat/{lamaran}/kirim', [CompanyChatController::class, 'send'])->name('chat.send');
    Route::get('/chat/{lamaran}/pesan', [CompanyChatController::class, 'messages'])->name('chat.messages');
});

Route::prefix('perusahaan')->name('companies.')->group(function () {
    Route::get('/', [PublicCompanyController::class, 'index'])->name('index');
    Route::get('/{perusahaan:slug}', [PublicCompanyController::class, 'show'])->name('show');
    Route::post('/{perusahaan:slug}/review', [PublicCompanyController::class, 'review'])->name('review')->middleware('auth');
});

Route::prefix('event')->name('events.')->group(function () {
    Route::get('/', [CompanyEventController::class, 'index'])->name('index');
    Route::get('/{event:slug}', [CompanyEventController::class, 'show'])->name('show');
    Route::post('/{event}/register', [CompanyEventController::class, 'register'])->name('register')->middleware('auth');
});

Route::prefix('artikel')->name('articles.')->group(function () {
    Route::get('/', [ArticleController::class, 'index'])->name('index');
    Route::get('/{article:slug}', [ArticleController::class, 'show'])->name('show');
});

Route::get('/tentang', [PageController::class, 'about'])->name('about');
Route::get('/kontak', [PageController::class, 'contact'])->name('contact');
Route::post('/kontak', [PageController::class, 'contactSend'])->name('contact.send');
Route::get('/faq', [PageController::class, 'faq'])->name('faq');
Route::get('/kebijakan-privasi', [PageController::class, 'privacy'])->name('privacy');
Route::get('/syarat-ketentuan', [PageController::class, 'terms'])->name('terms');

Route::get('/cari', [SearchController::class, 'search'])->name('search');

/* ================= SKILL ASSESSMENT & CAREER ROADMAP ================= */
Route::prefix('skill-assessment')->name('assessment.')->group(function () {
    Route::get('/', [AssessmentController::class, 'index'])->name('index');
    Route::get('/{assessment:slug}', [AssessmentController::class, 'show'])->name('show');
    Route::post('/{assessment:slug}/submit', [AssessmentController::class, 'submit'])->name('submit')->middleware('auth');
    Route::get('/{assessment:slug}/hasil', [AssessmentController::class, 'result'])->name('result')->middleware('auth');
    Route::get('/sertifikat/{result}', [AssessmentController::class, 'certificate'])->name('certificate')->middleware('auth');
});

Route::prefix('jalur-karier')->name('roadmaps.')->group(function () {
    Route::get('/', [RoadmapController::class, 'index'])->name('index');
    Route::get('/{roadmap:slug}', [RoadmapController::class, 'show'])->name('show');
    Route::post('/{roadmap:slug}/mulai', [RoadmapController::class, 'start'])->name('start')->middleware(['auth', 'role:pelamar']);
    Route::post('/{roadmap:slug}/langkah/{step}', [RoadmapController::class, 'toggleStep'])->name('step')->middleware(['auth', 'role:pelamar']);
});

/* Portofolio publik pelamar */
Route::get('/portofolio/{pelamar:user_id}', [CandidateProfileController::class, 'publicPortfolio'])->name('portfolio.public');

/* Follow perusahaan */
Route::post('/perusahaan/{perusahaan:slug}/follow', [PublicCompanyController::class, 'follow'])->name('companies.follow')->middleware('auth');

/* Halaman publik interview (QR Code) */
Route::get('/interview/{kode}', [PageController::class, 'publicInterview'])->name('interview.public');

/* ================= AUTH ================= */
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->middleware('throttle:5,1');
    Route::get('/daftar', [RegisterController::class, 'show'])->name('register');
    Route::post('/daftar', [RegisterController::class, 'register']);
    Route::get('/lupa-password', [ForgotPasswordController::class, 'show'])->name('password.request');
    Route::post('/lupa-password', [ForgotPasswordController::class, 'send'])->name('password.email');
    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'show'])->name('password.reset');
    Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');

    /* Social Login (Google & LinkedIn) */
    Route::get('/auth/{provider}/redirect', [SocialAuthController::class, 'redirect'])->whereIn('provider', ['google', 'linkedin-openid'])->name('social.redirect');
    Route::get('/auth/{provider}/callback', [SocialAuthController::class, 'callback'])->whereIn('provider', ['google', 'linkedin-openid'])->name('social.callback');
});
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

/* Verifikasi email (semua role, TANPA middleware verified) */
Route::middleware('auth')->group(function () {
    Route::get('/email/verify', [AuthVerificationController::class, 'notice'])->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}', [AuthVerificationController::class, 'verify'])->name('verification.verify');
    Route::post('/email/resend', [AuthVerificationController::class, 'resend'])->name('verification.send')->middleware('throttle:3,1');
});

/* Ganti password & preferensi (semua role, butuh verified) */
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/ganti-password', [PasswordController::class, 'show'])->name('password.change');
    Route::post('/ganti-password', [PasswordController::class, 'update'])->name('password.change.update');

    /* Preferensi notifikasi email (semua role) */
    Route::get('/pengaturan/notifikasi', [NotificationPrefController::class, 'index'])->name('notifikasi-prefs.index');
    Route::post('/pengaturan/notifikasi', [NotificationPrefController::class, 'update'])->name('notifikasi-prefs.update');

    /* Unduh surat penerimaan (pelamar pemilik / perusahaan pemilik / admin) */
    Route::get('/offer-letter/{lamaran}/unduh', [OfferLetterController::class, 'download'])->name('offer-letter.download');
});

/* ================= PELAMAR ================= */
Route::prefix('pelamar')->name('pelamar.')->middleware(['auth', 'verified', 'role:pelamar'])->group(function () {
    Route::get('/dashboard', [CandidateDashboardController::class, 'index'])->name('dashboard');
    Route::get('/profil', [CandidateProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profil', [CandidateProfileController::class, 'update'])->name('profile.update');
    Route::post('/profil/foto', [CandidateProfileController::class, 'uploadFoto'])->name('profile.uploadFoto');
    Route::post('/profil/foto/delete', [CandidateProfileController::class, 'deleteFoto'])->name('profile.deleteFoto');
    Route::post('/profil/cv', [CandidateProfileController::class, 'uploadCv'])->name('profile.uploadCv');
    Route::post('/profil/cv/delete', [CandidateProfileController::class, 'deleteCv'])->name('profile.deleteCv');
    Route::get('/profil/cv', [CandidateProfileController::class, 'downloadCv'])->name('profile.downloadCv');
    Route::get('/lamaran', [ApplicationController::class, 'index'])->name('lamaran.index');
    Route::get('/lamaran/{lamaran}', [ApplicationController::class, 'show'])->name('lamaran.show');
    Route::post('/lamaran/{lowongan}/apply', [ApplicationController::class, 'apply'])->name('lamaran.apply');
    Route::get('/tersimpan', [SavedJobController::class, 'index'])->name('saved.index');
    Route::post('/tersimpan/{lowongan}/toggle', [SavedJobController::class, 'toggle'])->name('saved.toggle');
    Route::get('/notifikasi', [CandidateNotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifikasi/{notification}/read', [CandidateNotificationController::class, 'read'])->name('notifications.read');
    Route::post('/notifikasi/read-all', [CandidateNotificationController::class, 'readAll'])->name('notifications.readAll');
    /* Chat */
    Route::get('/chat', [CandidateChatController::class, 'index'])->name('chat.index');
    Route::get('/chat/{lamaran}', [CandidateChatController::class, 'show'])->name('chat.show');
    Route::post('/chat/{lamaran}/kirim', [CandidateChatController::class, 'send'])->name('chat.send');
    Route::get('/chat/{lamaran}/pesan', [CandidateChatController::class, 'messages'])->name('chat.messages');
});

/* ================= SUPER ADMIN ================= */
Route::prefix('admin')->name('admin.')->middleware(['auth', 'verified', 'role:super_admin'])->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/verifikasi', [VerificationController::class, 'index'])->name('verifikasi.index');
    Route::get('/verifikasi/{perusahaan}/dokumen', [VerificationController::class, 'downloadDokumen'])->name('verifikasi.dokumen');
    Route::post('/verifikasi/{perusahaan}', [VerificationController::class, 'update'])->name('verifikasi.update');
    Route::get('/pengguna', [AdminUserController::class, 'index'])->name('users.index');
    Route::post('/pengguna/{user}/toggle', [AdminUserController::class, 'toggle'])->name('users.toggle');
    Route::delete('/pengguna/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');
    Route::get('/lowongan', [AdminLowonganController::class, 'index'])->name('lowongan.index');
    Route::post('/lowongan/{lowongan}/toggle', [AdminLowonganController::class, 'toggle'])->name('lowongan.toggle');
    Route::delete('/lowongan/{lowongan}', [AdminLowonganController::class, 'destroy'])->name('lowongan.destroy');
    /* Kelola konten */
    Route::get('/konten', [ContentController::class, 'index'])->name('content.index');
    Route::post('/konten/{resource}/store', [ContentController::class, 'store'])->name('content.store');
    Route::put('/konten/{resource}/{item}', [ContentController::class, 'update'])->name('content.update');
    Route::delete('/konten/{resource}/{item}', [ContentController::class, 'destroy'])->name('content.destroy');
    /* Notifikasi broadcast */
    Route::get('/notifikasi', [AdminNotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifikasi', [AdminNotificationController::class, 'broadcast'])->name('notifications.broadcast');
    /* Pengaturan kontak & media sosial */
    Route::get('/pengaturan', [AdminSettingController::class, 'index'])->name('settings.index');
    Route::post('/pengaturan', [AdminSettingController::class, 'update'])->name('settings.update');
    /* Aktivitas log */
    Route::get('/aktivitas', [ActivityLogController::class, 'index'])->name('activity.index');
    /* Laporan */
    Route::get('/laporan', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/laporan/export', [ReportController::class, 'export'])->name('reports.export');
});
