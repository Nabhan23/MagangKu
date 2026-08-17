<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Lamaran;
use App\Models\Lowongan;
use App\Models\PerusahaanProfile;
use App\Models\User;
use Dompdf\Dompdf;
use Illuminate\Http\Request;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Writer\XLSX\Writer as XlsxWriter;

class ReportController extends Controller
{
    public function index()
    {
        return view('admin.reports', ['data' => $this->collectData()]);
    }

    /**
     * Export laporan: ?format=pdf | excel | csv (default csv).
     */
    public function export(Request $request)
    {
        return match ($request->query('format', 'csv')) {
            'pdf' => $this->exportPdf(),
            'excel' => $this->exportExcel(),
            default => $this->exportCsv(),
        };
    }

    protected function collectData(): array
    {
        return [
            'totalUsers' => User::count(),
            'totalPelamar' => User::whereHas('role', fn ($q) => $q->where('slug', 'pelamar'))->count(),
            'totalPerusahaan' => User::whereHas('role', fn ($q) => $q->where('slug', 'perusahaan'))->count(),
            'totalLowongan' => Lowongan::count(),
            'totalLamaran' => Lamaran::count(),
            'lamaranPerStatus' => collect(Lamaran::STATUS)->mapWithKeys(fn ($v, $k) => [$k => Lamaran::where('status', $k)->count()]),
            'topPerusahaan' => PerusahaanProfile::withCount('lowonganAktif')->orderByDesc('lowongan_aktif_count')->take(5)->get(),
            'lamaranPerBulan' => collect(range(11, 0))->map(function ($i) {
                $date = now()->subMonths($i);
                return [
                    'date' => $date,
                    'jumlah' => Lamaran::whereYear('created_at', $date->year)->whereMonth('created_at', $date->month)->count(),
                ];
            }),
            'aktivitas' => ActivityLog::with('user')->latest()->take(20)->get(),
        ];
    }

    protected function rows(): array
    {
        $data = $this->collectData();
        $rows = [];
        $rows[] = ['Laporan MagangKu'];
        $rows[] = ['Tanggal', now()->translatedFormat('d F Y H:i')];
        $rows[] = [];
        $rows[] = ['Metrik', 'Jumlah'];
        $rows[] = ['Total Pengguna', $data['totalUsers']];
        $rows[] = ['Total Pelamar', $data['totalPelamar']];
        $rows[] = ['Total Perusahaan', $data['totalPerusahaan']];
        $rows[] = ['Total Lowongan', $data['totalLowongan']];
        $rows[] = ['Total Lamaran', $data['totalLamaran']];
        $rows[] = [];
        $rows[] = ['Status Lamaran', 'Jumlah'];
        foreach (Lamaran::STATUS as $key => $label) {
            $rows[] = [$label[0], Lamaran::where('status', $key)->count()];
        }

        return $rows;
    }

    protected function exportCsv()
    {
        $csv = fopen('php://temp', 'r+');
        foreach ($this->rows() as $row) {
            fputcsv($csv, $row);
        }
        rewind($csv);

        return response(stream_get_contents($csv), 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="laporan-magangku-' . now()->format('Y-m-d') . '.csv"',
        ]);
    }

    protected function exportPdf()
    {
        $data = $this->collectData();

        $html = view('admin.reports-pdf', ['data' => $data])->render();

        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="laporan-magangku-' . now()->format('Y-m-d') . '.pdf"',
        ]);
    }

    protected function exportExcel()
    {
        $writer = new XlsxWriter();
        $writer->openToBrowser('laporan-magangku-' . now()->format('Y-m-d') . '.xlsx');

        foreach ($this->rows() as $row) {
            $writer->addRow(Row::fromValues(array_map(fn ($v) => (string) $v, $row)));
        }

        $writer->close();

        return; // openToBrowser() sudah mengirim stream & header
    }
}
