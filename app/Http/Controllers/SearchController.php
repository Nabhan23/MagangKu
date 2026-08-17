<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\EventKarir;
use App\Models\Lowongan;
use App\Models\PerusahaanProfile;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $q = trim($request->get('q', ''));

        if ($q === '') {
            return redirect()->route('home');
        }

        $jobs = Lowongan::aktif()
            ->where(function ($builder) use ($q) {
                $builder->where('posisi', 'like', "%{$q}%")
                    ->orWhere('lokasi', 'like', "%{$q}%")
                    ->orWhereHas('perusahaan', fn ($p) => $p->where('nama_perusahaan', 'like', "%{$q}%"));
            })
            ->with('perusahaan')
            ->take(5)
            ->get();

        $companies = PerusahaanProfile::where('status_verifikasi', 'verified')
            ->where(function ($builder) use ($q) {
                $builder->where('nama_perusahaan', 'like', "%{$q}%")
                    ->orWhere('industri', 'like', "%{$q}%");
            })
            ->take(5)
            ->get();

        $events = EventKarir::where('status', 'aktif')
            ->where('judul', 'like', "%{$q}%")
            ->take(5)
            ->get();

        $articles = Article::published()
            ->where(function ($builder) use ($q) {
                $builder->where('judul', 'like', "%{$q}%")
                    ->orWhere('konten', 'like', "%{$q}%");
            })
            ->take(5)
            ->get();

        return view('search.index', compact('q', 'jobs', 'companies', 'events', 'articles'));
    }
}
