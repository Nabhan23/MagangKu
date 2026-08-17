<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\CompanyReview;
use App\Models\EventKarir;
use App\Models\Faq;
use App\Models\Lowongan;
use App\Models\PerusahaanProfile;
use App\Models\Role;
use App\Models\Testimonial;
use App\Models\User;

class LandingController extends Controller
{
    public function index()
    {
        $jobs = Lowongan::aktif()
            ->with(['perusahaan', 'skills'])
            ->latest()
            ->take(6)
            ->get();

        $magang = Lowongan::aktif()
            ->where('jenis', 'magang')
            ->with(['perusahaan'])
            ->latest()
            ->take(3)
            ->get();

        $companies = PerusahaanProfile::where('status_verifikasi', 'verified')
            ->withCount('lowonganAktif')
            ->get()
            ->sortByDesc('rating')
            ->take(10);

        $events = EventKarir::where('status', 'aktif')
            ->orderBy('tanggal_mulai')
            ->take(3)
            ->get();

        $articles = Article::published()
            ->latest('published_at')
            ->take(3)
            ->get();

        $testimonials = Testimonial::where('is_active', true)->get();

        // Statistik nyata dari database (bukan angka hardcoded)
        $stats = [
            'pelamar' => User::whereHas('role', fn ($q) => $q->where('slug', Role::PELAMAR))->count(),
            'perusahaan' => PerusahaanProfile::where('status_verifikasi', 'verified')->count(),
            'lowongan' => Lowongan::where('status', 'aktif')->count(),
            'magang' => Lowongan::where('status', 'aktif')->where('jenis', 'magang')->count(),
            'rating' => round((float) CompanyReview::where('status', 'approved')->avg('rating') ?? 0, 1),
            'ulasan' => CompanyReview::where('status', 'approved')->count(),
        ];

        return view('landing.index', compact(
            'jobs', 'magang', 'companies', 'events', 'articles', 'testimonials', 'stats'
        ));
    }
}
