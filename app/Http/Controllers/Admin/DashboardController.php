<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Article;
use App\Models\Category;
use App\Models\EventKarir;
use App\Models\Lamaran;
use App\Models\Lowongan;
use App\Models\PerusahaanProfile;
use App\Models\Skill;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'pelamar' => User::whereHas('role', fn ($q) => $q->where('slug', 'pelamar'))->count(),
            'perusahaan' => User::whereHas('role', fn ($q) => $q->where('slug', 'perusahaan'))->count(),
            'lowongan' => Lowongan::count(),
            'lamaran' => Lamaran::count(),
            'event' => EventKarir::count(),
            'artikel' => Article::count(),
            'menungguVerifikasi' => PerusahaanProfile::where('status_verifikasi', 'menunggu')->count(),
            'perusahaanTerverifikasi' => PerusahaanProfile::where('status_verifikasi', 'verified')->count(),
        ];

        // Grafik lamaran 6 bulan terakhir
        $grafikLamaran = collect(range(5, 0))->map(function ($i) {
            $date = now()->subMonths($i);
            return [
                'bulan' => $date->translatedFormat('M'),
                'jumlah' => Lamaran::whereYear('created_at', $date->year)->whereMonth('created_at', $date->month)->count(),
            ];
        });

        // Grafik pendaftar 6 bulan terakhir
        $grafikUser = collect(range(5, 0))->map(function ($i) {
            $date = now()->subMonths($i);
            return [
                'bulan' => $date->translatedFormat('M'),
                'pelamar' => User::whereHas('role', fn ($q) => $q->where('slug', 'pelamar'))->whereYear('created_at', $date->year)->whereMonth('created_at', $date->month)->count(),
                'perusahaan' => User::whereHas('role', fn ($q) => $q->where('slug', 'perusahaan'))->whereYear('created_at', $date->year)->whereMonth('created_at', $date->month)->count(),
            ];
        });

        $verifikasiTerbaru = PerusahaanProfile::where('status_verifikasi', 'menunggu')->with('user')->latest()->take(5)->get();
        $aktivitasTerbaru = ActivityLog::with('user')->latest()->take(8)->get();

        // Analytics dalam: lowongan paling dilirik, kategori, skill paling dicari
        $topLowongan = Lowongan::with('perusahaan')->orderByDesc('views')->take(10)->get();
        $lowonganPerKategori = Category::where('tipe', 'lowongan')->withCount('lowongans')->orderByDesc('lowongans_count')->get();
        $skillTerpopuler = Skill::withCount('lowongans')->orderByDesc('lowongans_count')->take(10)->get();

        return view('admin.dashboard', compact(
            'stats', 'grafikLamaran', 'grafikUser', 'verifikasiTerbaru', 'aktivitasTerbaru',
            'topLowongan', 'lowonganPerKategori', 'skillTerpopuler'
        ));
    }
}
