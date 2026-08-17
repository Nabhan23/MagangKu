<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\CompanyFollow;
use App\Models\CompanyReview;
use App\Models\Lamaran;
use App\Models\Notification;
use App\Models\PerusahaanProfile;
use Illuminate\Http\Request;

class PublicCompanyController extends Controller
{
    public function index(Request $request)
    {
        $query = PerusahaanProfile::where('status_verifikasi', 'verified')
            ->withCount(['lowonganAktif', 'reviews']);

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($builder) use ($q) {
                $builder->where('nama_perusahaan', 'like', "%{$q}%")
                    ->orWhere('industri', 'like', "%{$q}%")
                    ->orWhere('lokasi', 'like', "%{$q}%");
            });
        }

        if ($request->filled('industri')) {
            $query->where('industri', $request->industri);
        }

        $companies = $query->orderByDesc('is_featured')->paginate(12)->withQueryString();

        return view('companies.index', [
            'companies' => $companies,
            'industriList' => PerusahaanProfile::where('status_verifikasi', 'verified')->whereNotNull('industri')->distinct()->pluck('industri'),
            'q' => $request->q,
        ]);
    }

    public function show(PerusahaanProfile $perusahaan)
    {
        abort_if($perusahaan->status_verifikasi !== 'verified', 404);

        $perusahaan->load([
            'reviews' => fn ($q) => $q->where('status', 'approved')->with('pelamar'),
            'lowongans' => fn ($q) => $q->aktif(),
        ]);

        return view('companies.show', [
            'perusahaan' => $perusahaan,
            'myReview' => $this->myReview($perusahaan),
            'reviewEligible' => $this->reviewEligible($perusahaan),
        ]);
    }

    /**
     * Simpan ulasan & rating dari pelamar untuk perusahaan.
     * Hanya pelamar yang lamarannya lolos ke tahap interview / diterima.
     */
    public function review(Request $request, PerusahaanProfile $perusahaan)
    {
        $user = $request->user();
        $profile = $user?->pelamarProfile;

        if (!$user || !$profile) {
            return $this->reviewError('Hanya pelamar yang dapat memberi ulasan.', $request);
        }

        $eligible = $this->reviewEligible($perusahaan);
        if (!$eligible) {
            return $this->reviewError('Ulasan hanya dapat diberikan setelah lamaran Anda lolos ke tahap interview atau diterima di perusahaan ini.', $request);
        }

        if ($this->myReview($perusahaan)) {
            return $this->reviewError('Anda sudah memberikan ulasan untuk perusahaan ini.', $request);
        }

        $data = $request->validate([
            'rating' => ['required', 'integer', 'between:1,5'],
            'komentar' => ['required', 'string', 'max:1000'],
        ]);

        $review = CompanyReview::create([
            'perusahaan_profile_id' => $perusahaan->id,
            'pelamar_profile_id' => $profile->id,
            'rating' => $data['rating'],
            'komentar' => $data['komentar'],
            'status' => 'approved',
        ]);

        Notification::create([
            'user_id' => $perusahaan->user_id,
            'judul' => 'Ulasan Baru untuk Perusahaan Anda ⭐',
            'pesan' => "{$profile->nama} memberi rating {$data['rating']}/5: \"{$data['komentar']}\"",
            'type' => 'review',
            'link' => route('companies.show', $perusahaan),
        ]);

        ActivityLog::record('company_review', "Ulasan {$data['rating']}/5 untuk {$perusahaan->nama_perusahaan}", $user->id);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Terima kasih! Ulasan Anda telah dipublikasikan. ⭐',
                'rating' => $data['rating'],
                'komentar' => $data['komentar'],
                'nama' => $profile->nama,
                'url' => route('companies.show', $perusahaan),
            ]);
        }

        return back()->with('success', 'Terima kasih! Ulasan Anda telah dipublikasikan. ⭐');
    }

    /**
     * Ikuti / berhenti mengikuti perusahaan.
     */
    public function follow(Request $request, PerusahaanProfile $perusahaan)
    {
        $user = $request->user();
        abort_unless($user, 401);

        $existing = CompanyFollow::where('user_id', $user->id)
            ->where('perusahaan_profile_id', $perusahaan->id)
            ->first();

        if ($existing) {
            $existing->delete();
            $following = false;
        } else {
            CompanyFollow::create([
                'user_id' => $user->id,
                'perusahaan_profile_id' => $perusahaan->id,
            ]);
            $following = true;
        }

        return response()->json([
            'following' => $following,
            'count' => $perusahaan->followers()->count(),
        ]);
    }

    protected function myReview(PerusahaanProfile $perusahaan): ?CompanyReview
    {
        $profile = auth()->user()?->pelamarProfile;
        if (!$profile) {
            return null;
        }

        return CompanyReview::where('perusahaan_profile_id', $perusahaan->id)
            ->where('pelamar_profile_id', $profile->id)
            ->first();
    }

    protected function reviewEligible(PerusahaanProfile $perusahaan): bool
    {
        $profile = auth()->user()?->pelamarProfile;
        if (!$profile) {
            return false;
        }

        return Lamaran::where('pelamar_profile_id', $profile->id)
            ->whereIn('status', ['interview', 'diterima'])
            ->whereHas('lowongan', fn ($q) => $q->where('perusahaan_profile_id', $perusahaan->id))
            ->exists();
    }

    protected function reviewError(string $message, Request $request)
    {
        if ($request->wantsJson()) {
            return response()->json(['message' => $message], 422);
        }

        return back()->with('error', $message);
    }
}
