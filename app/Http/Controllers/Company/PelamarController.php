<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Mail\CompanyFeedbackMail;
use App\Mail\InterviewScheduledMail;
use App\Mail\LamaranStatusMail;
use App\Models\ActivityLog;
use App\Models\CompanyFeedback;
use App\Models\Interview;
use App\Models\Lamaran;
use App\Models\Lowongan;
use App\Models\Notification;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PelamarController extends Controller
{
    public function index(Request $request)
    {
        $perusahaan = auth()->user()->perusahaanProfile;
        $lowonganId = $request->get('lowongan');

        $query = Lamaran::whereIn('lowongan_id', $perusahaan->lowongans()->pluck('id'))
            ->with(['pelamar.user', 'lowongan']);

        if ($lowonganId) {
            $query->where('lowongan_id', $lowonganId);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $lamarans = $query->latest()->paginate(10)->withQueryString();

        return view('perusahaan.pelamar.index', [
            'lamarans' => $lamarans,
            'lowongans' => $perusahaan->lowongans,
            'statusList' => Lamaran::STATUS,
        ]);
    }

    public function show(Lamaran $lamaran)
    {
        $this->authorizeOwn($lamaran);

        return view('perusahaan.pelamar.show', [
            'lamaran' => $lamaran->load(['pelamar.user', 'lowongan', 'interview', 'feedback']),
            'statusList' => Lamaran::STATUS,
        ]);
    }

    public function downloadCv(Lamaran $lamaran)
    {
        $this->authorizeOwn($lamaran);

        $cv = $lamaran->pelamar->cv_path;
        if (!$cv || !Storage::disk('local')->exists($cv)) {
            return back()->with('error', 'CV tidak tersedia.');
        }

        return Storage::disk('local')->download($cv, 'CV-' . str_replace(' ', '-', $lamaran->pelamar->nama) . '.pdf');
    }

    public function updateStatus(Request $request, Lamaran $lamaran)
    {
        $this->authorizeOwn($lamaran);

        $request->validate(['status' => ['required', 'in:' . implode(',', array_keys(Lamaran::STATUS))]]);

        $old = $lamaran->status;
        $lamaran->update(['status' => $request->status]);

        // Hapus jadwal interview jika ditolak
        if ($request->status === 'ditolak' && $lamaran->interview) {
            $lamaran->interview->delete();
        }

        // Status diterima → otomatis buat surat penerimaan (offer letter)
        if ($request->status === 'diterima') {
            try {
                $this->generateOfferLetter($lamaran);
            } catch (\Throwable $e) {
                report($e);
            }
        }

        Notification::create([
            'user_id' => $lamaran->pelamar->user_id,
            'judul' => 'Status Lamaran Diperbarui',
            'pesan' => "Status lamaran Anda untuk posisi \"{$lamaran->lowongan->posisi}\" berubah dari {$this->label($old)} menjadi {$this->label($request->status)}.",
            'type' => 'lamaran',
            'link' => route('pelamar.lamaran.show', $lamaran),
        ]);

        // Kirim email notifikasi ke pelamar (sesuai preferensi)
        if ($lamaran->pelamar->user->wantsEmail('status_lamaran')) {
            try {
                Mail::to($lamaran->pelamar->user->email)->send(new LamaranStatusMail($lamaran, $old));
            } catch (\Throwable $e) {
                report($e);
            }
        }

        ActivityLog::record('lamaran_status', "Ubah status lamaran #{$lamaran->id}: {$old} → {$request->status}", auth()->id());

        return response()->json(['message' => 'Status lamaran diperbarui dan email notifikasi terkirim.']);
    }

    public function scheduleInterview(Request $request, Lamaran $lamaran)
    {
        $this->authorizeOwn($lamaran);

        $data = $request->validate([
            'tanggal' => ['required', 'date', 'after_or_equal:today'],
            'waktu' => ['required'],
            'tipe' => ['required', 'in:online,offline'],
            'link' => ['nullable', 'url', 'max:255'],
            'lokasi' => ['nullable', 'string', 'max:255'],
            'catatan' => ['nullable', 'string', 'max:1000'],
        ]);

        $interview = Interview::firstOrNew(['lamaran_id' => $lamaran->id]);
        $interview->fill($data);
        $interview->status = 'dijadwalkan';
        $interview->created_by = auth()->id();
        if (! $interview->kode) {
            $interview->kode = strtoupper(Str::random(8));
        }
        $interview->save();

        $lamaran->update(['status' => 'interview']);

        $detail = $data['tipe'] === 'online' && $data['link']
            ? "Link: {$data['link']}"
            : ($data['lokasi'] ?? 'Lokasi menyusul');

        Notification::create([
            'user_id' => $lamaran->pelamar->user_id,
            'judul' => 'Interview Dijadwalkan',
            'pesan' => "Interview untuk posisi \"{$lamaran->lowongan->posisi}\" pada {$data['tanggal']} pukul {$data['waktu']}. {$detail}",
            'type' => 'interview',
            'link' => route('pelamar.lamaran.show', $lamaran),
        ]);

        // Kirim email notifikasi interview ke pelamar (sesuai preferensi)
        if ($lamaran->pelamar->user->wantsEmail('interview')) {
            try {
                Mail::to($lamaran->pelamar->user->email)->send(new InterviewScheduledMail($lamaran, $interview));
            } catch (\Throwable $e) {
                report($e);
            }
        }

        ActivityLog::record('interview_schedule', "Jadwalkan interview lamaran #{$lamaran->id}", auth()->id());

        return response()->json(['message' => 'Interview berhasil dijadwalkan dan email notifikasi terkirim.']);
    }

    public function bulkStatus(Request $request)
    {
        $request->validate([
            'status' => ['required', 'in:' . implode(',', array_keys(Lamaran::STATUS))],
            'lamaran_ids' => ['required', 'array', 'min:1'],
            'lamaran_ids.*' => ['integer'],
        ]);

        $perusahaan = auth()->user()->perusahaanProfile;
        $lamarans = Lamaran::whereIn('id', $request->lamaran_ids)
            ->whereIn('lowongan_id', $perusahaan->lowongans()->pluck('id'))
            ->get();

        if ($lamarans->isEmpty()) {
            return back()->with('error', 'Tidak ada pelamar yang dipilih.');
        }

        $count = 0;
        foreach ($lamarans as $lamaran) {
            if ($lamaran->status === $request->status) {
                continue;
            }

            $old = $lamaran->status;
            $lamaran->update(['status' => $request->status]);

            // Hapus jadwal interview jika ditolak
            if ($request->status === 'ditolak' && $lamaran->interview) {
                $lamaran->interview->delete();
            }

            Notification::create([
                'user_id' => $lamaran->pelamar->user_id,
                'judul' => 'Status Lamaran Diperbarui',
                'pesan' => "Status lamaran Anda untuk posisi \"{$lamaran->lowongan->posisi}\" berubah dari {$this->label($old)} menjadi {$this->label($request->status)}.",
                'type' => 'lamaran',
                'link' => route('pelamar.lamaran.show', $lamaran),
            ]);

            if ($lamaran->pelamar->user->wantsEmail('status_lamaran')) {
                try {
                    Mail::to($lamaran->pelamar->user->email)->send(new LamaranStatusMail($lamaran, $old));
                } catch (\Throwable $e) {
                    report($e);
                }
            }

            $count++;
        }

        ActivityLog::record('lamaran_bulk_status', "Ubah status {$count} lamaran menjadi {$request->status}", auth()->id());

        return back()->with('success', "Status {$count} pelamar diubah menjadi {$this->label($request->status)}.");
    }

    public function giveFeedback(Request $request, Lamaran $lamaran)
    {
        $this->authorizeOwn($lamaran);

        $data = $request->validate([
            'rating' => ['required', 'integer', 'between:1,5'],
            'feedback' => ['nullable', 'string', 'max:2000'],
        ]);

        $feedback = CompanyFeedback::updateOrCreate(
            ['lamaran_id' => $lamaran->id],
            [
                'perusahaan_profile_id' => auth()->user()->perusahaanProfile->id,
                'rating' => $data['rating'],
                'feedback' => $data['feedback'] ?? null,
            ]
        );

        Notification::create([
            'user_id' => $lamaran->pelamar->user_id,
            'judul' => 'Feedback dari Perusahaan',
            'pesan' => "Perusahaan \"{$lamaran->lowongan->perusahaan->nama_perusahaan}\" memberikan feedback untuk lamaran \"{$lamaran->lowongan->posisi}\".",
            'type' => 'feedback',
            'link' => route('pelamar.lamaran.show', $lamaran),
        ]);

        // Kirim email feedback ke pelamar (sesuai preferensi)
        if ($lamaran->pelamar->user->wantsEmail('feedback')) {
            try {
                Mail::to($lamaran->pelamar->user->email)->send(new CompanyFeedbackMail($feedback));
            } catch (\Throwable $e) {
                report($e);
            }
        }

        ActivityLog::record('feedback', "Kirim feedback pelamar #{$lamaran->id}", auth()->id());

        return response()->json(['message' => 'Feedback berhasil dikirim ke pelamar.']);
    }

    /**
     * Export pelamar per lowongan: ?format=csv | excel | pdf.
     */
    public function export(Request $request, Lowongan $lowongan)
    {
        $this->authorizeOwnLowongan($lowongan);

        return match ($request->query('format', 'csv')) {
            'excel' => $this->exportExcel($lowongan),
            'pdf' => $this->exportPdf($lowongan),
            default => $this->exportCsv($lowongan),
        };
    }

    protected function exportRows(Lowongan $lowongan): array
    {
        $rows = [];
        $rows[] = ['Nama', 'Email', 'Universitas', 'Jurusan', 'Lokasi', 'Status', 'Tanggal Lamar'];
        foreach ($lowongan->lamarans()->with('pelamar.user')->get() as $lamaran) {
            $rows[] = [
                $lamaran->pelamar->nama,
                $lamaran->pelamar->user->email,
                $lamaran->pelamar->universitas,
                $lamaran->pelamar->jurusan,
                $lamaran->pelamar->lokasi,
                $lamaran->status_label,
                $lamaran->created_at->format('d M Y'),
            ];
        }

        return $rows;
    }

    protected function exportCsv(Lowongan $lowongan)
    {
        $csv = fopen('php://temp', 'r+');
        foreach ($this->exportRows($lowongan) as $row) {
            fputcsv($csv, $row);
        }
        rewind($csv);

        return response(stream_get_contents($csv), 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="pelamar-' . $lowongan->slug . '.csv"',
        ]);
    }

    protected function exportExcel(Lowongan $lowongan)
    {
        $writer = new \OpenSpout\Writer\XLSX\Writer();
        $writer->openToBrowser('pelamar-' . $lowongan->slug . '.xlsx');

        foreach ($this->exportRows($lowongan) as $row) {
            $writer->addRow(\OpenSpout\Common\Entity\Row::fromValues(array_map(fn ($v) => (string) $v, $row)));
        }

        $writer->close();

        return;
    }

    protected function exportPdf(Lowongan $lowongan)
    {
        $lamarans = $lowongan->lamarans()->with('pelamar.user')->get();

        $html = view('perusahaan.laporan-pelamar-pdf', [
            'lowongan' => $lowongan,
            'lamarans' => $lamarans,
        ])->render();

        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="pelamar-' . $lowongan->slug . '.pdf"',
        ]);
    }

    protected function authorizeOwn(Lamaran $lamaran): void
    {
        $perusahaan = auth()->user()->perusahaanProfile;
        abort_unless($perusahaan->lowongans()->where('id', $lamaran->lowongan_id)->exists(), 403);
    }

    protected function authorizeOwnLowongan(Lowongan $lowongan): void
    {
        abort_unless($lowongan->perusahaan_profile_id === auth()->user()->perusahaanProfile->id, 403);
    }

    protected function generateOfferLetter(Lamaran $lamaran): void
    {
        if ($lamaran->offer_letter_path && Storage::disk('local')->exists($lamaran->offer_letter_path)) {
            return; // sudah pernah dibuat
        }

        $lamaran->load(['pelamar', 'lowongan.perusahaan']);

        $pdf = Pdf::loadView('pdf.offer-letter', [
            'lamaran' => $lamaran,
            'nomor' => 'MK/' . $lamaran->id . '/OFF/' . now()->format('Y'),
            'tanggal' => now()->translatedFormat('d F Y'),
        ]);

        $path = 'offer-letters/offer-letter-' . $lamaran->id . '-' . Str::random(8) . '.pdf';
        Storage::disk('local')->put($path, $pdf->output());
        $lamaran->update(['offer_letter_path' => $path]);
    }

    protected function label(string $status): string
    {
        return Lamaran::STATUS[$status][0] ?? $status;
    }
}
