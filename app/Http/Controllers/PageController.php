<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Faq;
use App\Models\Interview;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function about()
    {
        return view('pages.about');
    }

    public function contact()
    {
        return view('pages.contact');
    }

    public function contactSend(Request $request)
    {
        $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email'],
            'subjek' => ['required', 'string', 'max:255'],
            'pesan' => ['required', 'string', 'max:2000'],
        ]);

        ActivityLog::record('contact', "Pesan dari {$request->nama} ({$request->email}): {$request->subjek}");

        return back()->with('success', 'Pesan Anda telah terkirim. Tim MagangKu akan segera menghubungi Anda.');
    }

    public function faq()
    {
        return view('pages.faq', ['faqs' => Faq::where('is_active', true)->orderBy('urutan')->get()]);
    }

    /**
     * Halaman publik interview (dibuka lewat QR Code).
     */
    public function publicInterview(string $kode)
    {
        $interview = Interview::with(['lamaran.lowongan.perusahaan', 'lamaran.pelamar.user'])->where('kode', $kode)->firstOrFail();

        return view('pages.interview', ['interview' => $interview]);
    }

    public function privacy()
    {
        return view('pages.privacy');
    }

    public function terms()
    {
        return view('pages.terms');
    }
}
