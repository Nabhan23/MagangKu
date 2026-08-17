<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\CompanyVerificationMail;
use App\Models\ActivityLog;
use App\Models\Notification;
use App\Models\PerusahaanProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class VerificationController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status', 'menunggu');

        $perusahaan = PerusahaanProfile::with('user')
            ->when($status !== 'semua', fn ($q) => $q->where('status_verifikasi', $status))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('admin.verifikasi', [
            'perusahaan' => $perusahaan,
            'activeStatus' => $status,
            'counts' => [
                'menunggu' => PerusahaanProfile::where('status_verifikasi', 'menunggu')->count(),
                'verified' => PerusahaanProfile::where('status_verifikasi', 'verified')->count(),
                'rejected' => PerusahaanProfile::where('status_verifikasi', 'rejected')->count(),
            ],
        ]);
    }

    public function downloadDokumen(PerusahaanProfile $perusahaan)
    {
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

    public function update(Request $request, PerusahaanProfile $perusahaan)
    {
        $request->validate([
            'status' => ['required', 'in:verified,rejected'],
            'pesan' => ['nullable', 'string', 'max:500'],
        ]);

        $perusahaan->update([
            'status_verifikasi' => $request->status,
            'pesan_verifikasi' => $request->pesan,
        ]);

        $judul = $request->status === 'verified' ? 'Perusahaan Terverifikasi 🎉' : 'Verifikasi Ditolak';
        $pesan = $request->status === 'verified'
            ? "Selamat! Perusahaan \"{$perusahaan->nama_perusahaan}\" telah terverifikasi. Anda kini bisa memposting lowongan."
            : "Verifikasi perusahaan \"{$perusahaan->nama_perusahaan}\" ditolak. " . ($request->pesan ?: 'Silakan hubungi admin.');

        Notification::create([
            'user_id' => $perusahaan->user_id,
            'judul' => $judul,
            'pesan' => $pesan,
            'type' => 'verifikasi',
            'link' => route('perusahaan.profile.edit'),
        ]);

        // Kirim email konfirmasi ke perusahaan (sesuai preferensi)
        if ($perusahaan->user->wantsEmail('verifikasi')) {
            try {
                Mail::to($perusahaan->user->email)->send(new CompanyVerificationMail($perusahaan, $request->status, $request->pesan));
            } catch (\Throwable $e) {
                report($e);
            }
        }

        ActivityLog::record('company_verify', "Verifikasi {$perusahaan->nama_perusahaan}: {$request->status}", auth()->id());

        return back()->with('success', "Perusahaan {$perusahaan->nama_perusahaan} berhasil {$request->status} dan email konfirmasi terkirim.");
    }
}
