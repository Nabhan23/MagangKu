<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    public function edit()
    {
        return view('perusahaan.profile', ['perusahaan' => auth()->user()->perusahaanProfile]);
    }

    public function update(Request $request)
    {
        $perusahaan = auth()->user()->perusahaanProfile;

        $data = $request->validate([
            'nama_perusahaan' => ['required', 'string', 'max:255'],
            'industri' => ['nullable', 'string', 'max:255'],
            'lokasi' => ['nullable', 'string', 'max:255'],
            'alamat_lengkap' => ['nullable', 'string', 'max:1000'],
            'deskripsi' => ['nullable', 'string', 'max:3000'],
            'website' => ['nullable', 'url', 'max:255'],
            'email_kontak' => ['nullable', 'email', 'max:255'],
            'telepon' => ['nullable', 'string', 'max:20'],
            'ukuran_perusahaan' => ['nullable', 'string', 'max:50'],
            'tahun_berdiri' => ['nullable', 'integer', 'between:1900,2030'],
        ]);

        if (isset($data['nama_perusahaan']) && $data['nama_perusahaan'] !== $perusahaan->nama_perusahaan) {
            $data['slug'] = Str::slug($data['nama_perusahaan']) . '-' . Str::random(4);
        }

        $perusahaan->update($data);
        $perusahaan->user->update(['name' => $data['nama_perusahaan']]);

        ActivityLog::record('company_profile_update', 'Perusahaan memperbarui profil', auth()->id());

        return back()->with('success', 'Profil perusahaan berhasil diperbarui.');
    }

    public function uploadLogo(Request $request)
    {
        $request->validate(['logo' => ['required', 'image', 'mimes:jpeg,png,jpg,webp,svg', 'max:2048']]);

        $path = $request->file('logo')->store('logo-perusahaan', 'public');
        auth()->user()->perusahaanProfile->update(['logo' => $path]);

        return back()->with('success', 'Logo berhasil diunggah.');
    }

    public function uploadDokumen(Request $request)
    {
        $request->validate(['dokumen' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120']]);

        $perusahaan = auth()->user()->perusahaanProfile;
        // Simpan di disk privat (bukan public) — dokumen verifikasi bersifat rahasia.
        $path = $request->file('dokumen')->store('dokumen-verifikasi', 'local');
        $perusahaan->update([
            'dokumen_verifikasi' => $path,
            'status_verifikasi' => 'menunggu',
        ]);

        ActivityLog::record('company_verify_request', 'Perusahaan mengirim dokumen verifikasi', auth()->id());

        return back()->with('success', 'Dokumen verifikasi dikirim. Menunggu persetujuan admin.');
    }

    public function downloadDokumen()
    {
        $perusahaan = auth()->user()->perusahaanProfile;

        $path = $perusahaan->dokumen_verifikasi;
        if (!$path) {
            return back()->with('error', 'Dokumen tidak tersedia.');
        }

        // Cek disk privat dulu, lalu public (file lama sebelum perbaikan)
        if (Storage::disk('local')->exists($path)) {
            return Storage::disk('local')->response($path);
        }
        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->response($path);
        }

        return back()->with('error', 'Dokumen tidak ditemukan.');
    }
}
