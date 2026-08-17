<?php

namespace App\Http\Controllers\Candidate;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $profile = $user->pelamarProfile;

        $roadmap = $profile->career_roadmap_id ? $profile->roadmap()->first() : null;
        $roadmapPersen = $roadmap && $roadmap->total_tahapan
            ? (int) round((count($profile->roadmap_progress ?? []) / $roadmap->total_tahapan) * 100)
            : 0;

        return view('pelamar.dashboard', [
            'profile' => $profile,
            'totalLamaran' => $profile->lamarans()->count(),
            'lamaranAktif' => $profile->lamarans()->whereIn('status', ['menunggu', 'diproses', 'psikotes', 'interview'])->count(),
            'diterima' => $profile->lamarans()->where('status', 'diterima')->count(),
            'totalSimpan' => $user->savedJobs()->count(),
            'notifUnread' => $user->unreadNotifications()->count(),
            'riwayatTerbaru' => $profile->lamarans()->with('lowongan.perusahaan')->latest()->take(5)->get(),
            'roadmap' => $roadmap,
            'roadmapPersen' => $roadmapPersen,
            'hasilTesTerbaru' => $user->skillAssessmentResults()->with('assessment')->latest()->take(2)->get(),
        ]);
    }
}
