<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\PelamarProfile;
use App\Models\PerusahaanProfile;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    public function show()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $role = $request->input('role');

        $data = $request->validate([
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'role' => ['required', 'in:pelamar,perusahaan'],
        ]);

        if ($role === Role::PELAMAR) {
            $data += $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'phone' => ['nullable', 'string', 'max:20'],
            ]);
        } else {
            $data += $request->validate([
                'nama_perusahaan' => ['required', 'string', 'max:255'],
                'industri' => ['required', 'string', 'max:100'],
                'lokasi' => ['required', 'string', 'max:255'],
                'alamat_lengkap' => ['nullable', 'string', 'max:1000'],
                'telepon' => ['nullable', 'string', 'max:20'],
                'website' => ['nullable', 'url', 'max:255'],
                'ukuran_perusahaan' => ['nullable', 'in:1-10,11-50,51-200,201-500,500+'],
                'tahun_berdiri' => ['nullable', 'integer', 'between:1900,2030'],
                'deskripsi' => ['nullable', 'string', 'max:2000'],
            ]);
        }

        $roleModel = Role::where('slug', $data['role'])->firstOrFail();

        $user = DB::transaction(function () use ($data, $roleModel) {
            $user = User::create([
                'name' => $data['role'] === Role::PELAMAR ? $data['name'] : $data['nama_perusahaan'],
                'email' => $data['email'],
                'password' => $data['password'],
                'phone' => $data['phone'] ?? ($data['telepon'] ?? null),
                'role_id' => $roleModel->id,
            ]);

            if ($data['role'] === Role::PELAMAR) {
                PelamarProfile::create([
                    'user_id' => $user->id,
                    'nama_lengkap' => $data['name'],
                    'lokasi' => 'Jakarta',
                ]);
            } else {
                PerusahaanProfile::create([
                    'user_id' => $user->id,
                    'nama_perusahaan' => $data['nama_perusahaan'],
                    'slug' => Str::slug($data['nama_perusahaan']) . '-' . Str::random(4),
                    'industri' => $data['industri'],
                    'lokasi' => $data['lokasi'],
                    'alamat_lengkap' => $data['alamat_lengkap'] ?? null,
                    'telepon' => $data['telepon'] ?? null,
                    'website' => $data['website'] ?? null,
                    'ukuran_perusahaan' => $data['ukuran_perusahaan'] ?? null,
                    'tahun_berdiri' => $data['tahun_berdiri'] ?? null,
                    'deskripsi' => $data['deskripsi'] ?? null,
                    'email_kontak' => $data['email'],
                    'status_verifikasi' => 'menunggu',
                ]);
            }

            return $user;
        });

        ActivityLog::record('register', 'Pendaftaran akun baru: ' . $user->email, $user->id);

        auth()->login($user);

        // User baru wajib verifikasi email dulu
        $user->sendEmailVerificationNotification();

        return redirect()->route('verification.notice')
            ->with('success', 'Daftar berhasil! Silakan verifikasi email Anda untuk mengaktifkan akun.');
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
