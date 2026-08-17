<?php

namespace App\Http\Controllers\Candidate;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\PelamarProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    public function edit()
    {
        return view('pelamar.profile', ['profile' => auth()->user()->pelamarProfile]);
    }

    public function update(Request $request)
    {
        $profile = auth()->user()->pelamarProfile;

        // Normalisasi field URL: tambahkan https:// bila skema tidak ditulis
        foreach (['website_portfolio', 'linkedin', 'github'] as $field) {
            if ($request->filled($field) && !preg_match('#^https?://#i', $request->input($field))) {
                $request->merge([$field => 'https://' . $request->input($field)]);
            }
        }

        $data = $request->validate([
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'tanggal_lahir' => ['nullable', 'date'],
            'tempat_lahir' => ['nullable', 'string', 'max:255'],
            'jenis_kelamin' => ['nullable', 'in:L,P'],
            'universitas' => ['nullable', 'string', 'max:255'],
            'jurusan' => ['nullable', 'string', 'max:255'],
            'pendidikan_terakhir' => ['nullable', 'string', 'max:50'],
            'tahun_lulus' => ['nullable', 'integer', 'between:1980,2030'],
            'lokasi' => ['nullable', 'string', 'max:255'],
            'alamat' => ['nullable', 'string', 'max:500'],
            'ekspektasi_gaji' => ['nullable', 'string', 'max:100'],
            'tentang' => ['nullable', 'string', 'max:2000'],
            'keahlian' => ['nullable', 'string', 'max:500'],
            'website_portfolio' => ['nullable', 'url', 'max:255'],
            'linkedin' => ['nullable', 'url', 'max:255'],
            'github' => ['nullable', 'url', 'max:255'],
            'is_portfolio_public' => ['nullable', 'boolean'],
        ]);

        $profile->update($data + ['is_portfolio_public' => $request->boolean('is_portfolio_public', true)]);
        $profile->user->update(['name' => $data['nama_lengkap'], 'phone' => $request->phone]);

        // Badge CV lengkap
        $lengkap = $profile->foto && $profile->cv_path && $profile->universitas && $profile->tentang;
        if ($lengkap && $profile->badge === 'pelamar_aktif') {
            $profile->update(['badge' => 'cv_lengkap']);
        }

        ActivityLog::record('profile_update', 'Pelamar memperbarui profil', auth()->id());

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    public function uploadFoto(Request $request)
    {
        $request->validate(['foto' => ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048']]);

        $user = auth()->user();
        $path = $request->file('foto')->store('foto-pelamar', 'public');
        $user->pelamarProfile->update(['foto' => $path]);

        return back()->with('success', 'Foto profil berhasil diunggah.');
    }

    public function deleteFoto(Request $request)
    {
        $profile = auth()->user()->pelamarProfile;

        if ($profile->foto) {
            Storage::disk('public')->delete($profile->foto);
            $profile->update(['foto' => null]);
        }

        ActivityLog::record('profile_update', 'Pelamar menghapus foto profil', auth()->id());

        return back()->with('success', 'Foto profil berhasil dihapus.');
    }

    public function uploadCv(Request $request)
    {
        $request->validate(['cv' => ['required', 'file', 'mimes:pdf,doc,docx', 'max:5120']]);

        $path = $request->file('cv')->store('cv-pelamar', 'local');
        auth()->user()->pelamarProfile->update(['cv_path' => $path]);

        ActivityLog::record('cv_upload', 'Pelamar mengunggah CV', auth()->id());

        return back()->with('success', 'CV berhasil diunggah.');
    }

    public function deleteCv(Request $request)
    {
        $profile = auth()->user()->pelamarProfile;

        if ($profile->cv_path) {
            Storage::disk('local')->delete($profile->cv_path);
            $profile->update(['cv_path' => null]);
        }

        ActivityLog::record('cv_delete', 'Pelamar menghapus CV', auth()->id());

        return back()->with('success', 'CV berhasil dihapus.');
    }

    /**
     * Unduh/lihat CV milik sendiri (hanya pemilik profil yang login).
     */
    public function downloadCv()
    {
        $profile = auth()->user()->pelamarProfile;

        if (! $profile->cv_path || ! Storage::disk('local')->exists($profile->cv_path)) {
            abort(404, 'CV belum tersedia.');
        }

        return Storage::disk('local')->response($profile->cv_path);
    }

    public function publicPortfolio(int $userId)
    {
        $profile = PelamarProfile::where('user_id', $userId)
            ->where('is_portfolio_public', true)
            ->firstOrFail();

        $profile->increment('views');

        return view('pelamar.portfolio', [
            'profile' => $profile->load(['user', 'lamarans.lowongan.perusahaan']),
        ]);
    }
}
