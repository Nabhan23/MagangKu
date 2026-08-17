<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerificationController extends Controller
{
    /**
     * Halaman "cek email Anda" (verification.notice).
     */
    public function notice()
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            return redirect()->intended($this->redirectTo());
        }

        // Kirim otomatis sekali per sesi agar halaman tidak kosong
        if (session()->missing('verification_sent')) {
            $user->sendEmailVerificationNotification();
            session(['verification_sent' => true]);
        }

        return view('auth.verify');
    }

    /**
     * Klik link verifikasi dari email: /email/verify/{id}/{hash}
     */
    public function verify(Request $request, int $id, string $hash)
    {
        $user = User::findOrFail($id);

        if (! hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            abort(403, 'Link verifikasi tidak valid.');
        }

        if ($user->hasVerifiedEmail()) {
            return redirect($this->redirectTo())->with('success', 'Email Anda sudah terverifikasi.');
        }

        $user->markEmailAsVerified();

        ActivityLog::record('email_verify', 'Verifikasi email: ' . $user->email, $user->id);

        return redirect($this->redirectTo())->with('success', 'Email Anda berhasil diverifikasi. Selamat datang di MagangKu 🎉');
    }

    /**
     * Kirim ulang email verifikasi (POST /email/resend) — dibatasi 3x/menit.
     */
    public function resend(Request $request)
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            return redirect($this->redirectTo());
        }

        $user->sendEmailVerificationNotification();

        return back()->with('success', 'Email verifikasi baru telah dikirim. Silakan cek inbox Anda.');
    }

    protected function redirectTo(): string
    {
        $user = auth()->user();
        if ($user->isSuperAdmin()) {
            return route('admin.dashboard');
        }
        if ($user->isPerusahaan()) {
            return route('perusahaan.dashboard');
        }
        return route('pelamar.dashboard');
    }
}
