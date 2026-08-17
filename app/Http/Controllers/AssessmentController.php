<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Lamaran;
use App\Models\Lowongan;
use App\Models\Notification;
use App\Models\PelamarProfile;
use App\Models\SkillAssessment;
use App\Models\SkillAssessmentResult;
use Illuminate\Http\Request;

class AssessmentController extends Controller
{
    /** Skor minimal untuk lulus */
    public const BATAS_LULUS = 70;

    /** Skor minimal untuk badge Top Talent */
    public const BATAS_TOP_TALENT = 85;

    public function index()
    {
        $assessments = SkillAssessment::where('status', 'aktif')
            ->withCount('questions')
            ->with('results')
            ->orderBy('kategori')
            ->get();

        return view('assessment.index', [
            'assessments' => $assessments,
            'kategoriList' => SkillAssessment::KATEGORI,
        ]);
    }

    /**
     * Halaman mengerjakan tes. Butuh login (jawaban tidak boleh bocor ke publik).
     */
    public function show(SkillAssessment $assessment)
    {
        abort_if($assessment->status !== 'aktif', 404);

        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu untuk mengikuti Skill Assessment.');
        }

        if (!$user->isPelamar()) {
            abort(403, 'Hanya akun pelamar yang dapat mengikuti Skill Assessment.');
        }

        $latest = $user->skillAssessmentResults()
            ->where('skill_assessment_id', $assessment->id)
            ->latest()
            ->first();

        return view('assessment.show', [
            'assessment' => $assessment->load('questions'),
            'latest' => $latest,
        ]);
    }

    /**
     * Proses submit jawaban, hitung skor, simpan hasil, beri badge Top Talent.
     */
    public function submit(Request $request, SkillAssessment $assessment)
    {
        abort_if($assessment->status !== 'aktif', 404);

        $user = $request->user();
        abort_unless($user && $user->isPelamar(), 403);

        $questions = $assessment->questions()->get();
        if ($questions->isEmpty()) {
            return back()->with('error', 'Tes ini belum memiliki soal.');
        }

        $jawaban = $request->input('jawaban', []);

        $benar = 0;
        $detail = [];
        foreach ($questions as $q) {
            $picked = $jawaban[$q->id] ?? null;
            $isCorrect = $picked === $q->jawaban_benar;
            if ($isCorrect) {
                $benar++;
            }
            $detail[] = [
                'question_id' => $q->id,
                'picked' => $picked,
                'correct' => $q->jawaban_benar,
                'is_correct' => $isCorrect,
                'pembahasan' => $q->pembahasan,
            ];
        }

        $total = $questions->count();
        $skor = (int) round(($benar / $total) * 100);
        $status = $skor >= self::BATAS_LULUS ? 'lulus' : 'gagal';

        $result = SkillAssessmentResult::create([
            'user_id' => $user->id,
            'skill_assessment_id' => $assessment->id,
            'skor' => $skor,
            'total_soal' => $total,
            'benar' => $benar,
            'salah' => $total - $benar,
            'detail' => $detail,
            'status' => $status,
        ]);

        // Badge Top Talent untuk skor tinggi
        $profile = $user->pelamarProfile;
        if ($profile && $skor >= self::BATAS_TOP_TALENT && !in_array($profile->badge, ['top_talent', 'interview_expert'], true)) {
            $profile->update(['badge' => 'top_talent']);
            Notification::create([
                'user_id' => $user->id,
                'judul' => 'Badge Top Talent Diraih! 🏆',
                'pesan' => "Selamat! Skor Anda {$skor} pada \"{$assessment->judul}\" masuk kategori Top Talent. Badge ini meningkatkan visibilitas Anda di mata recruiter.",
                'type' => 'assessment',
                'link' => route('assessment.result', $assessment),
            ]);
        }

        ActivityLog::record('skill_assessment', "Menyelesaikan Skill Assessment \"{$assessment->judul}\" dengan skor {$skor}", $user->id);

        $url = route('assessment.result', [$assessment, 'result' => $result->id]);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Tes selesai! Skor kamu ' . $skor . '.',
                'redirect' => $url,
                'skor' => $skor,
                'status' => $status,
            ]);
        }

        return redirect($url);
    }

    /**
     * Halaman hasil tes.
     */
    public function result(Request $request, SkillAssessment $assessment)
    {
        $user = auth()->user();
        abort_unless($user && $user->isPelamar(), 403);

        $result = SkillAssessmentResult::where('id', $request->result)
            ->where('user_id', $user->id)
            ->where('skill_assessment_id', $assessment->id)
            ->with('assessment.questions')
            ->firstOrFail();

        // Rekomendasi lowongan berdasarkan kategori tes
        $keyword = match ($assessment->kategori) {
            'teknologi' => ['Laravel', 'Developer', 'Programmer'],
            'desain' => ['Designer', 'Desain'],
            'marketing' => ['Marketing', 'Digital'],
            'keuangan' => ['Keuangan', 'Audit', 'Akuntansi'],
            'psikotes' => ['Administrasi', 'Staff', 'Umum'],
            default => [],
        };
        $rekomendasi = Lowongan::aktif()->where(function ($q) use ($keyword) {
            foreach ($keyword as $k) {
                $q->orWhere('posisi', 'like', "%{$k}%");
            }
        })->take(3)->get();

        return view('assessment.result', [
            'assessment' => $assessment,
            'result' => $result,
            'rekomendasi' => $rekomendasi,
            'batasLulus' => self::BATAS_LULUS,
            'batasTopTalent' => self::BATAS_TOP_TALENT,
        ]);
    }

    /**
     * Sertifikat digital otomatis untuk hasil tes yang lulus.
     */
    public function certificate(SkillAssessmentResult $result)
    {
        $user = auth()->user();
        abort_unless($user && $user->isPelamar(), 403);
        abort_unless($result->user_id === $user->id, 403);
        abort_if($result->status !== 'lulus', 404, 'Sertifikat hanya tersedia untuk hasil tes yang lulus.');

        return view('assessment.certificate', [
            'result' => $result->load('assessment', 'user.pelamarProfile'),
        ]);
    }
}
