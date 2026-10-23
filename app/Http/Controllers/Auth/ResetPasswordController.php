<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ResetPasswordController extends Controller
{
    /** Token reset kedaluwarsa setelah 60 menit (selaras dengan config/auth.php). */
    public const TOKEN_EXPIRE_MINUTES = 60;

    public function show(string $token)
    {
        return view('auth.reset', ['token' => $token]);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $record = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        // Token di-hash saat disimpan, jadi verifikasi pakai Hash::check.
        // Tolak juga token yang sudah kedaluwarsa (dibuat > 60 menit lalu).
        $expired = ! $record
            || ! $record->created_at
            || \Carbon\Carbon::parse($record->created_at)
                ->addMinutes(self::TOKEN_EXPIRE_MINUTES)
                ->isPast();

        if ($expired || ! Hash::check($request->token, $record->token)) {
            return back()->withErrors(['email' => 'Tautan reset tidak valid atau sudah kedaluwarsa.']);
        }

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return back()->withErrors(['email' => 'Email tidak ditemukan.']);
        }

        $user->update(['password' => Hash::make($request->password)]);
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('login')->with('success', 'Password berhasil direset. Silakan login.');
    }
}
