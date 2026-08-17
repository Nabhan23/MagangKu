<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\EventKarir;
use App\Models\Lowongan;
use App\Models\PerusahaanProfile;
use App\Models\User;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function jobs(Request $request)
    {
        $query = Lowongan::aktif()
            ->select(['id', 'perusahaan_profile_id', 'category_id', 'posisi', 'slug', 'jenis', 'deskripsi', 'persyaratan', 'gaji_min', 'gaji_max', 'lokasi', 'is_remote', 'kuota', 'deadline', 'created_at'])
            ->with(['perusahaan:id,nama_perusahaan,slug,logo,industri,lokasi', 'skills:id,nama']);

        if ($request->filled('q')) {
            $query->where('posisi', 'like', "%{$request->q}%");
        }
        if ($request->filled('jenis')) {
            $query->where('jenis', $request->jenis);
        }
        if ($request->filled('lokasi')) {
            $query->where('lokasi', 'like', "%{$request->lokasi}%");
        }

        return response()->json([
            'success' => true,
            'data' => $query->paginate($request->get('per_page', 10))->withQueryString(),
        ]);
    }

    public function jobDetail(Lowongan $lowongan)
    {
        $lowongan->setHidden(['created_by']);

        return response()->json([
            'success' => true,
            'data' => $lowongan->load([
                'skills',
                'category',
                'perusahaan' => fn ($q) => $q->setHidden(['user_id', 'dokumen_verifikasi', 'status_verifikasi', 'pesan_verifikasi']),
                'perusahaan.reviews' => fn ($q) => $q->select(['id', 'rating', 'komentar', 'created_at']),
            ]),
        ]);
    }

    public function companies()
    {
        $companies = PerusahaanProfile::where('status_verifikasi', 'verified')
            ->withCount('lowonganAktif')
            ->get()
            ->makeHidden(['user_id', 'dokumen_verifikasi', 'status_verifikasi', 'pesan_verifikasi'])
            ->sortByDesc('rating')
            ->values();

        return response()->json(['success' => true, 'data' => $companies]);
    }

    public function events()
    {
        return response()->json([
            'success' => true,
            'data' => EventKarir::where('status', 'aktif')->withCount('registrations')->orderBy('tanggal_mulai')->get(),
        ]);
    }

    public function articles()
    {
        return response()->json([
            'success' => true,
            'data' => Article::published()->latest('published_at')->get(),
        ]);
    }

    public function stats()
    {
        return response()->json([
            'success' => true,
            'data' => [
                'pelamar' => User::whereHas('role', fn ($q) => $q->where('slug', 'pelamar'))->count(),
                'perusahaan' => User::whereHas('role', fn ($q) => $q->where('slug', 'perusahaan'))->count(),
                'lowongan' => Lowongan::aktif()->count(),
                'magang' => Lowongan::aktif()->where('jenis', 'magang')->count(),
            ],
        ]);
    }

    public function search(Request $request)
    {
        $q = $request->get('q', '');

        $jobs = $q ? Lowongan::aktif()
            ->select(['id', 'perusahaan_profile_id', 'category_id', 'posisi', 'slug', 'jenis', 'deskripsi', 'persyaratan', 'gaji_min', 'gaji_max', 'lokasi', 'is_remote', 'kuota', 'deadline', 'created_at'])
            ->where('posisi', 'like', "%{$q}%")->with('perusahaan:id,nama_perusahaan,slug,logo,industri,lokasi')->get() : collect();
        $companies = $q ? PerusahaanProfile::where('status_verifikasi', 'verified')
            ->where('nama_perusahaan', 'like', "%{$q}%")->get()->makeHidden(['user_id', 'dokumen_verifikasi', 'status_verifikasi', 'pesan_verifikasi']) : collect();
        $events = $q ? EventKarir::where('status', 'aktif')->where('judul', 'like', "%{$q}%")->get() : collect();

        return response()->json([
            'success' => true,
            'data' => compact('jobs', 'companies', 'events'),
        ]);
    }
}
