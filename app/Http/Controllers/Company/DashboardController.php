<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        $perusahaan = auth()->user()->perusahaanProfile;

        $stats = [
            'totalLowongan' => $perusahaan->lowongans()->count(),
            'lowonganAktif' => $perusahaan->lowonganAktif()->count(),
            'totalPelamar' => \App\Models\Lamaran::whereIn('lowongan_id', $perusahaan->lowongans()->pluck('id'))->count(),
            'interview' => \App\Models\Interview::whereIn('lamaran_id', \App\Models\Lamaran::whereIn('lowongan_id', $perusahaan->lowongans()->pluck('id'))->pluck('id'))->where('status', 'dijadwalkan')->count(),
        ];

        $lamaranTerbaru = \App\Models\Lamaran::whereIn('lowongan_id', $perusahaan->lowongans()->pluck('id'))
            ->with(['pelamar.user', 'lowongan'])
            ->latest()
            ->take(6)
            ->get();

        // Lowongan dengan lamaran terbanyak (top 5)
        $lamaranPerLowongan = \App\Models\Lowongan::whereIn('id', $perusahaan->lowongans()->pluck('id'))
            ->withCount('lamarans')
            ->orderByDesc('lamarans_count')
            ->take(5)
            ->get(['id', 'posisi', 'lamarans_count']);

        // Distribusi status lamaran perusahaan
        $distribusiStatus = collect(\App\Models\Lamaran::STATUS)->mapWithKeys(fn ($v, $k) => [
            $k => \App\Models\Lamaran::whereIn('lowongan_id', $perusahaan->lowongans()->pluck('id'))->where('status', $k)->count(),
        ]);

        // Rata-rata pelamar per lowongan
        $totalLowongan = max($perusahaan->lowongans()->count(), 1);
        $rataPelamar = round($stats['totalPelamar'] / $totalLowongan, 1);

        // Statistik per bulan (6 bulan terakhir)
        $bulan = collect(range(5, 0))->map(function ($i) {
            $date = now()->subMonths($i);
            $count = \App\Models\Lamaran::whereIn('lowongan_id', auth()->user()->perusahaanProfile->lowongans()->pluck('id'))
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            return ['bulan' => $date->translatedFormat('M'), 'jumlah' => $count];
        });

        return view('perusahaan.dashboard', compact('perusahaan', 'stats', 'lamaranTerbaru', 'bulan', 'lamaranPerLowongan', 'distribusiStatus', 'rataPelamar'));
    }
}
