<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\CareerRoadmap;
use App\Models\Lowongan;
use Illuminate\Http\Request;

class RoadmapController extends Controller
{
    public function index()
    {
        $roadmaps = CareerRoadmap::where('status', 'aktif')->orderBy('bidang')->get();

        return view('roadmaps.index', [
            'roadmaps' => $roadmaps,
            'bidangList' => CareerRoadmap::BIDANG,
        ]);
    }

    public function show(CareerRoadmap $roadmap)
    {
        abort_if($roadmap->status !== 'aktif', 404);

        // Lowongan terkait berdasarkan judul/bidang roadmap
        $keywords = explode(' ', $roadmap->judul);
        $lowongan = Lowongan::aktif()->where(function ($q) use ($keywords, $roadmap) {
            foreach ($keywords as $k) {
                $q->orWhere('posisi', 'like', "%{$k}%");
            }
            $q->orWhere('deskripsi', 'like', "%{$roadmap->bidang}%");
        })->take(4)->get();

        $profile = auth()->user()?->pelamarProfile;

        return view('roadmaps.show', [
            'roadmap' => $roadmap,
            'lowongan' => $lowongan,
            'profile' => $profile,
            'progress' => $profile && $profile->career_roadmap_id === $roadmap->id ? ($profile->roadmap_progress ?? []) : [],
        ]);
    }

    /**
     * Pelamar mulai mengikuti sebuah jalur karier.
     */
    public function start(Request $request, CareerRoadmap $roadmap)
    {
        abort_if($roadmap->status !== 'aktif', 404);

        $profile = $request->user()->pelamarProfile;
        $profile->update([
            'career_roadmap_id' => $roadmap->id,
            'roadmap_progress' => [],
        ]);

        ActivityLog::record('roadmap_start', "Mulai jalur karier \"{$roadmap->judul}\"", $request->user()->id);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Jalur karier berhasil dimulai! 🚀']);
        }

        return back()->with('success', 'Jalur karier berhasil dimulai! 🚀');
    }

    /**
     * Tandai / batalkan penyelesaian satu tahapan (AJAX).
     */
    public function toggleStep(Request $request, CareerRoadmap $roadmap, int $step)
    {
        $profile = $request->user()->pelamarProfile;

        if ($profile->career_roadmap_id !== $roadmap->id) {
            return response()->json(['message' => 'Mulai jalur karier ini terlebih dahulu.'], 422);
        }

        $total = count($roadmap->tahapan ?? []);
        if ($step < 0 || $step >= $total) {
            return response()->json(['message' => 'Tahapan tidak valid.'], 422);
        }

        $progress = $profile->roadmap_progress ?? [];
        $progress = in_array($step, $progress, true)
            ? array_values(array_diff($progress, [$step]))
            : array_merge($progress, [$step]);
        sort($progress);

        $profile->update(['roadmap_progress' => $progress]);

        $persen = $total ? (int) round((count($progress) / $total) * 100) : 0;

        return response()->json([
            'message' => 'Progress diperbarui.',
            'progress' => $progress,
            'persen' => $persen,
            'selesai' => $persen === 100,
        ]);
    }
}
