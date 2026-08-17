<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Interview;
use App\Models\Lamaran;
use Carbon\Carbon;
use Illuminate\Http\Request;

class InterviewController extends Controller
{
    public function calendar(Request $request)
    {
        $perusahaan = auth()->user()->perusahaanProfile;
        $lamaranIds = Lamaran::whereIn('lowongan_id', $perusahaan->lowongans()->pluck('id'))->pluck('id');

        // Bulan yang dilihat (default: bulan ini)
        $month = $request->filled('bulan') ? Carbon::createFromFormat('Y-m', $request->bulan) : Carbon::now()->startOfMonth();

        $interviews = Interview::whereIn('lamaran_id', $lamaranIds)
            ->with(['lamaran.pelamar.user', 'lamaran.lowongan'])
            ->whereYear('tanggal', $month->year)
            ->whereMonth('tanggal', $month->month)
            ->orderBy('tanggal')
            ->orderBy('waktu')
            ->get()
            ->groupBy(fn ($i) => $i->tanggal->format('Y-m-d'));

        // Bangun grid kalender (Minggu = hari pertama)
        $start = $month->copy()->startOfMonth();
        $end = $month->copy()->endOfMonth();
        $days = [];
        $cursor = $start->copy()->startOfWeek(Carbon::SUNDAY);
        while ($cursor->lte($end)) {
            $days[] = [
                'date' => $cursor->copy(),
                'inMonth' => $cursor->month === $month->month,
                'interviews' => $interviews[$cursor->format('Y-m-d')] ?? collect(),
            ];
            $cursor->addDay();
        }

        return view('perusahaan.interview-calendar', [
            'month' => $month,
            'days' => $days,
            'total' => $interviews->flatten()->count(),
        ]);
    }
}
