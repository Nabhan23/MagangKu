<?php

namespace App\Http\Controllers;

use App\Models\Lowongan;
use Illuminate\Http\Request;

class JobController extends Controller
{
    public function index(Request $request)
    {
        $query = Lowongan::aktif()->with(['perusahaan', 'skills', 'category']);

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($builder) use ($q) {
                $builder->where('posisi', 'like', "%{$q}%")
                    ->orWhere('lokasi', 'like', "%{$q}%")
                    ->orWhereHas('perusahaan', fn ($p) => $p->where('nama_perusahaan', 'like', "%{$q}%"));
            });
        }

        if ($request->filled('jenis')) {
            $jenis = $request->jenis;
            $query->whereIn('jenis', (array) $jenis);
        }

        if ($request->filled('lokasi')) {
            $query->where('lokasi', 'like', "%{$request->lokasi}%");
        }

        if ($request->filled('kategori')) {
            $query->where('category_id', $request->kategori);
        }

        if ($request->boolean('remote')) {
            $query->where('is_remote', true);
        }

        $sort = $request->get('sort', 'terbaru');
        $query->when($sort === 'gaji', fn ($q) => $q->orderByDesc('gaji_max'))
            ->when($sort === 'deadline', fn ($q) => $q->orderBy('deadline'))
            ->when($sort === 'terbaru', fn ($q) => $q->latest());

        if ($request->ajax()) {
            $jobs = $query->paginate(9)->withQueryString();
            return response()->json([
                'html' => view('jobs.partials.list', ['jobs' => $jobs])->render(),
                'pagination' => view('jobs.partials.pagination', ['jobs' => $jobs])->render(),
                'count' => $jobs->total(),
            ]);
        }

        $jobs = $query->paginate(9)->withQueryString();

        return view('jobs.index', [
            'jobs' => $jobs,
            'jenisList' => Lowongan::JENIS_LABEL,
            'kategoriList' => \App\Models\Category::where('tipe', 'lowongan')->get(),
            'filters' => $request->only(['q', 'jenis', 'lokasi', 'kategori', 'remote', 'sort']),
            'rekomendasi' => $this->rekomendasi(),
        ]);
    }

    /**
     * Rekomendasi lowongan pintar: cocokkan skill di profil pelamar
     * dengan skill yang dibutuhkan lowongan + nama posisi.
     */
    protected function rekomendasi(): \Illuminate\Support\Collection
    {
        $user = auth()->user();
        if (! $user?->isPelamar() || ! $user->pelamarProfile?->keahlian) {
            return collect();
        }

        $skills = collect(preg_split('/[,;]|\s+dan\s+/i', $user->pelamarProfile->keahlian))
            ->map(fn ($s) => mb_strtolower(trim($s)))
            ->filter()
            ->values();

        if ($skills->isEmpty()) {
            return collect();
        }

        return Lowongan::aktif()->with(['skills', 'perusahaan'])->get()
            ->map(function (Lowongan $l) use ($skills) {
                $namaPosisi = mb_strtolower($l->posisi);
                $skor = 0;

                foreach ($l->skills as $skill) {
                    $namaSkill = mb_strtolower($skill->nama);
                    if ($skills->contains(fn ($k) => str_contains($namaSkill, $k) || str_contains($k, $namaSkill))) {
                        $skor += 2;
                    }
                }

                // Bonus jika skill pelamar muncul di nama posisi (mis. Laravel, Data Analyst)
                $skor += $skills->filter(fn ($k) => str_contains($namaPosisi, $k))->count();

                return ['lowongan' => $l, 'skor' => $skor];
            })
            ->filter(fn ($r) => $r['skor'] > 0)
            ->sortByDesc('skor')
            ->take(6)
            ->map(fn ($r) => $r['lowongan'])
            ->values();
    }

    public function show(Lowongan $lowongan)
    {
        abort_if($lowongan->status !== 'aktif' && !auth()->user()?->isSuperAdmin(), 404);

        $lowongan->increment('views');

        $related = Lowongan::aktif()
            ->where('id', '!=', $lowongan->id)
            ->where('jenis', $lowongan->jenis)
            ->with('perusahaan')
            ->latest()
            ->take(3)
            ->get();

        return view('jobs.show', [
            'lowongan' => $lowongan->load(['perusahaan', 'skills', 'category', 'perusahaan.reviews.pelamar']),
            'related' => $related,
            'sudahDilamar' => $this->sudahDilamar($lowongan),
            'disimpan' => $this->disimpan($lowongan),
        ]);
    }

    protected function sudahDilamar(Lowongan $lowongan): bool
    {
        $user = auth()->user();
        if (!$user?->pelamarProfile) {
            return false;
        }
        return $lowongan->lamarans()->where('pelamar_profile_id', $user->pelamarProfile->id)->exists();
    }

    protected function disimpan(Lowongan $lowongan): bool
    {
        $user = auth()->user();
        if (!$user) {
            return false;
        }
        return $lowongan->savedBy()->where('user_id', $user->id)->exists();
    }
}
