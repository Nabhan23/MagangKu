<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\PasswordResetMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    public function show()
    {
        return view('auth.forgot');
    }

    public function send(Request $request)
    {
        $request->validate(['email' => ['required', 'email']]);

        $user = User::where('email', $request->email)->first();

        // Selalu kembalikan respons yang sama (keamanan - anti account enumeration)
        if (!$user) {
            return back()->with('status', 'Jika email terdaftar, tautan reset password telah dikirim.');
        }

        $token = Str::random(64);

        // Simpan HASH dari token (bukan token plaintext) agar kebocoran database
        // tidak serta-merta memberi penyerang tautan reset yang valid.
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            ['token' => Hash::make($token), 'created_at' => now()]
        );

        // Mode pengembangan: tampilkan tautan reset langsung di layar.
        // HANYA di lingkungan lokal — JANGAN pernah mengekspos token di produksi
        // karena siapa pun yang meminta reset untuk email korban bisa mengambil alih akun.
        if (app()->environment('local')) {
            return back()->with('status', 'Jika email terdaftar, tautan reset password telah dikirim.')
                ->with('reset_link', route('password.reset', $token));
        }

        // Di produksi, kirim tautan lewat email (konfigurasi SMTP di .env:
        // MAIL_MAILER, MAIL_HOST, MAIL_PORT, MAIL_USERNAME, MAIL_PASSWORD).
        Mail::to($user->email)->send(new PasswordResetMail($token, ResetPasswordController::TOKEN_EXPIRE_MINUTES));

        return back()->with('status', 'Jika email terdaftar, tautan reset password telah dikirim.');
    }
}
