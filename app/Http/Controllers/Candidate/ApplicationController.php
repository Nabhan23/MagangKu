<?php

namespace App\Http\Controllers\Candidate;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Lamaran;
use App\Models\Lowongan;
use App\Models\Notification;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    public function index()
    {
        $lamarans = auth()->user()->pelamarProfile
            ->lamarans()
            ->with(['lowongan.perusahaan', 'interview'])
            ->latest()
            ->paginate(8);

        return view('pelamar.lamaran', ['lamarans' => $lamarans]);
    }

    public function show(Lamaran $lamaran)
    {
        abort_unless($lamaran->pelamar_profile_id === auth()->user()->pelamarProfile->id, 403);

        return view('pelamar.lamaran-show', [
            'lamaran' => $lamaran->load(['lowongan.perusahaan', 'interview']),
        ]);
    }

    public function apply(Request $request, Lowongan $lowongan)
    {
        $profile = auth()->user()->pelamarProfile;

        if ($lowongan->status !== 'aktif' || $lowongan->deadline < now()->toDateString()) {
            return response()->json(['message' => 'Lowongan sudah tidak menerima lamaran.'], 422);
        }

        if (!$profile->cv_path) {
            return response()->json(['message' => 'Unggah CV terlebih dahulu sebelum melamar.'], 422);
        }

        $exists = Lamaran::where('lowongan_id', $lowongan->id)
            ->where('pelamar_profile_id', $profile->id)
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'Anda sudah melamar lowongan ini.'], 422);
        }

        $request->validate([
            'catatan' => ['nullable', 'string', 'max:1000'],
            'file_pendukung' => ['nullable', 'file', 'mimes:pdf,doc,docx,zip', 'max:5120'],
        ]);

        $filePath = $request->hasFile('file_pendukung')
            ? $request->file('file_pendukung')->store('file-lamaran', 'public')
            : null;

        $lamaran = Lamaran::create([
            'lowongan_id' => $lowongan->id,
            'pelamar_profile_id' => $profile->id,
            'status' => 'menunggu',
            'catatan' => $request->catatan,
            'file_pendukung' => $filePath,
        ]);

        // Notifikasi ke perusahaan
        Notification::create([
            'user_id' => $lowongan->perusahaan->user_id,
            'judul' => 'Lamaran Baru Masuk',
            'pesan' => "{$profile->nama} melamar posisi \"{$lowongan->posisi}\".",
            'type' => 'lamaran',
            'link' => route('perusahaan.pelamar.show', $lamaran),
        ]);

        ActivityLog::record('apply', "Melamar posisi {$lowongan->posisi}", auth()->id());

        return response()->json([
            'message' => 'Lamaran berhasil dikirim! Pantau statusnya di menu Tracking Lamaran.',
            'url' => route('pelamar.lamaran.show', $lamaran),
        ]);
    }
}
