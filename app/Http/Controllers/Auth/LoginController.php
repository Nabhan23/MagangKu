<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function show()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors(['email' => 'Email atau password salah.'])->onlyInput('email');
        }

        $user = Auth::user();

        if (!$user->is_active) {
            Auth::logout();
            return back()->withErrors(['email' => 'Akun Anda telah dinonaktifkan. Hubungi admin.']);
        }

        ActivityLog::record('login', 'Login berhasil', $user->id);

        $request->session()->regenerate();

        // Email belum diverifikasi → arahkan ke halaman verifikasi
        if (! $user->hasVerifiedEmail()) {
            return redirect()->route('verification.notice');
        }

        return redirect()->intended($this->redirectTo())
            ->with('success', 'Login berhasil! Selamat datang kembali, ' . $user->name . ' 👋');
    }

    public function logout(Request $request)
    {
        ActivityLog::record('logout', 'Logout', auth()->id());
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home');
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
