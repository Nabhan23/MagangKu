<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Lamaran;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class OfferLetterController extends Controller
{
    /**
     * Generate & simpan surat penerimaan (hanya perusahaan pemilik lowongan,
     * hanya saat status lamaran = diterima).
     */
    public function generate(Request $request, Lamaran $lamaran)
    {
        $user = auth()->user();
        if (! $user->isPerusahaan()) {
            abort(403);
        }
        if ($lamaran->lowongan->perusahaan_profile_id !== $user->perusahaanProfile->id) {
            abort(403);
        }
        if ($lamaran->status !== 'diterima') {
            return back()->withErrors('Surat penerimaan hanya bisa dibuat saat status lamaran sudah "Diterima".');
        }

        $lamaran->load(['pelamar', 'lowongan.perusahaan']);

        $pdf = Pdf::loadView('pdf.offer-letter', [
            'lamaran' => $lamaran,
            'nomor' => 'MK/' . $lamaran->id . '/OFF/' . now()->format('Y'),
            'tanggal' => now()->translatedFormat('d F Y'),
        ]);

        $path = 'offer-letters/offer-letter-' . $lamaran->id . '-' . Str::random(8) . '.pdf';
        Storage::disk('local')->put($path, $pdf->output());
        $lamaran->update(['offer_letter_path' => $path]);

        ActivityLog::record('offer_letter', "Generate surat penerimaan lamaran #{$lamaran->id}", $user->id);

        return back()->with('success', 'Surat penerimaan berhasil dibuat dan tersedia untuk pelamar.');
    }

    /**
     * Unduh surat penerimaan — hanya pelamar pemilik lamaran, perusahaan
     * pemilik lowongan, atau admin.
     */
    public function download(Lamaran $lamaran)
    {
        $user = auth()->user();
        if (! $user) {
            abort(403);
        }

        $isPelamar = $user->isPelamar() && $lamaran->pelamar?->user_id === $user->id;
        $isPerusahaan = $user->isPerusahaan() && $lamaran->lowongan->perusahaan_profile_id === $user->perusahaanProfile?->id;
        $isAdmin = $user->isSuperAdmin();

        if (! $isPelamar && ! $isPerusahaan && ! $isAdmin) {
            abort(403);
        }
        if (! $lamaran->offer_letter_path || ! Storage::disk('local')->exists($lamaran->offer_letter_path)) {
            abort(404, 'Surat penerimaan belum tersedia.');
        }

        $nama = Str::slug($lamaran->pelamar->nama_lengkap ?? 'pelamar');

        return Storage::disk('local')->download(
            $lamaran->offer_letter_path,
            "Surat-Penerimaan-{$nama}.pdf"
        );
    }
}
