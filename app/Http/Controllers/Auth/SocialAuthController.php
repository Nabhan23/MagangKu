<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\PelamarProfile;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    protected array $providers = ['google', 'linkedin-openid'];

    /**
     * Alihkan ke halaman OAuth provider.
     */
    public function redirect(string $provider)
    {
        if (! in_array($provider, $this->providers, true)) {
            abort(404);
        }

        if (! config("services.{$provider}.client_id") || ! config("services.{$provider}.client_secret")) {
            return redirect()->route('login')->withErrors([
                'email' => 'Login dengan ' . ucfirst(str_replace('-openid', '', $provider)) . ' belum dikonfigurasi. Silakan hubungi admin.',
            ]);
        }

        return Socialite::driver($provider)->redirect();
    }

    /**
     * Tangani callback dari provider OAuth.
     */
    public function callback(Request $request, string $provider)
    {
        if (! in_array($provider, $this->providers, true)) {
            abort(404);
        }

        try {
            $social = Socialite::driver($provider)->user();
        } catch (\Throwable $e) {
            return redirect()->route('login')->withErrors(['email' => 'Gagal login dengan ' . ucfirst(str_replace('-openid', '', $provider)) . '. Coba lagi.']);
        }

        $email = $social->getEmail();
        if (! $email) {
            return redirect()->route('login')->withErrors(['email' => 'Akun ' . ucfirst(str_replace('-openid', '', $provider)) . ' tidak memiliki email. Gunakan email lain.']);
        }

        // Cari user: cocokkan dulu via provider, lalu via email
        $user = User::where('provider', $provider)->where('provider_id', $social->getId())->first()
            ?? User::where('email', $email)->first();

        if (! $user) {
            $user = $this->createUserFromSocial($provider, $social, $email);
        } else {
            // Tautkan provider ke akun yang sudah ada
            $user->update([
                'provider' => $provider,
                'provider_id' => $social->getId(),
            ]);
        }

        if (! $user->is_active) {
            return redirect()->route('login')->withErrors(['email' => 'Akun Anda dinonaktifkan.']);
        }

        Auth::login($user);

        // Regenerasi session setelah login untuk mencegah session fixation.
        $request->session()->regenerate();

        return redirect()->intended(match (true) {
            $user->isSuperAdmin() => route('admin.dashboard'),
            $user->isPerusahaan() => route('perusahaan.dashboard'),
            default => route('pelamar.dashboard'),
        });
    }

    protected function createUserFromSocial(string $provider, $social, string $email): User
    {
        $name = $social->getName() ?: explode('@', $email)[0];

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Str::password(24),
            'role_id' => Role::where('slug', Role::PELAMAR)->value('id'),
            'provider' => $provider,
            'provider_id' => $social->getId(),
            // Email dari provider sudah diverifikasi oleh Google/GitHub
            'email_verified_at' => now(),
        ]);

        // Foto profil dari provider (opsional)
        if ($social->getAvatar()) {
            try {
                $contents = @file_get_contents($social->getAvatar());
                if ($contents !== false) {
                    $dir = public_path('storage/avatars');
                    File::ensureDirectoryExists($dir);
                    $file = 'avatars/' . Str::random(20) . '.jpg';
                    File::put(public_path('storage/') . $file, $contents);
                    $user->update(['avatar' => $file]);
                }
            } catch (\Throwable) {
                // Abaikan bila gagal mengunduh foto
            }
        }

        PelamarProfile::create([
            'user_id' => $user->id,
            'nama_lengkap' => $name,
        ]);

        Notification::create([
            'user_id' => $user->id,
            'judul' => 'Selamat Datang di MagangKu! 🎉',
            'pesan' => 'Akun berhasil dibuat melalui login sosial. Lengkapi profil dan unggah CV untuk mulai melamar.',
            'type' => 'broadcast',
            'link' => route('pelamar.profile.edit'),
        ]);

        return $user;
    }
}
