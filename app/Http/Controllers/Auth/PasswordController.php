<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PasswordController extends Controller
{
    public function show()
    {
        return view('auth.change-password');
    }

    public function update(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'string'],
            'new_password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if (! Hash::check($request->current_password, auth()->user()->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini salah.']);
        }

        auth()->user()->update(['password' => $request->new_password]);

        // Invalidasi sesi lain bila didukung
        try {
            auth()->logoutOtherDevices($request->new_password);
        } catch (\Throwable $e) {
            // abaikan bila AuthenticateSession tidak tersedia
        }

        ActivityLog::record('password_change', 'Pengguna mengganti password', auth()->id());

        return back()->with('success', 'Password berhasil diganti. Gunakan password baru saat login berikutnya.');
    }
}
