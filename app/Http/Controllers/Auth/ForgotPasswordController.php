<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

        // Selalu kembalikan respons yang sama (keamanan)
        if (!$user) {
            return back()->with('status', 'Jika email terdaftar, tautan reset password telah dikirim.');
        }

        $token = Str::random(64);
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            ['token' => $token, 'created_at' => now()]
        );

        // Mode pengembangan: tampilkan tautan reset langsung di layar
        // (di produksi gunakan Mail untuk mengirim tautan ini)
        $link = route('password.reset', $token);

        return back()->with('status', 'Jika email terdaftar, tautan reset password telah dikirim.')
            ->with('reset_link', $link);
    }
}
