<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Sistem - MagangKu</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1E3A5F; }
        h1 { font-size: 20px; color: #0288D1; margin: 0 0 2px; }
        .sub { color: #6c757d; margin-bottom: 4px; }
        h2 { font-size: 13px; border-bottom: 2px solid #4FC3F7; padding-bottom: 4px; margin-top: 18px; margin-bottom: 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { border: 1px solid #d0d7de; padding: 5px 8px; text-align: left; }
        th { background: #E3F2FD; }
        .footer { margin-top: 24px; color: #9aa4b2; font-size: 9px; text-align: center; }
    </style>
</head>
<body>
    <h1>Laporan Sistem MagangKu</h1>
    <div class="sub">Dibuat: {{ now()->translatedFormat('d F Y H:i') }} &nbsp;•&nbsp; Tagline: "Langkah Awal Menuju Karier Impian"</div>

    <h2>Ringkasan Sistem</h2>
    <table>
        <tr><th>Metrik</th><th>Jumlah</th></tr>
        <tr><td>Total Pengguna</td><td>{{ number_format($data['totalUsers']) }}</td></tr>
        <tr><td>Pelamar</td><td>{{ number_format($data['totalPelamar']) }}</td></tr>
        <tr><td>Perusahaan</td><td>{{ number_format($data['totalPerusahaan']) }}</td></tr>
        <tr><td>Total Lowongan</td><td>{{ number_format($data['totalLowongan']) }}</td></tr>
        <tr><td>Total Lamaran</td><td>{{ number_format($data['totalLamaran']) }}</td></tr>
    </table>

    <h2>Distribusi Status Lamaran</h2>
    <table>
        <tr><th>Status</th><th>Jumlah</th></tr>
        @foreach($data['lamaranPerStatus'] as $key => $count)
            <tr><td>{{ \App\Models\Lamaran::STATUS[$key][0] }}</td><td>{{ $count }}</td></tr>
        @endforeach
    </table>

    <h2>Lamaran per Bulan (12 Bulan Terakhir)</h2>
    <table>
        <tr><th>Bulan</th><th>Jumlah Lamaran</th></tr>
        @foreach($data['lamaranPerBulan'] as $b)
            <tr><td>{{ $b['date']->translatedFormat('F Y') }}</td><td>{{ $b['jumlah'] }}</td></tr>
        @endforeach
    </table>

    <h2>Perusahaan Paling Aktif</h2>
    <table>
        <tr><th>#</th><th>Perusahaan</th><th>Industri</th><th>Lowongan Aktif</th></tr>
        @foreach($data['topPerusahaan'] as $i => $p)
            <tr><td>{{ $i + 1 }}</td><td>{{ $p->nama_perusahaan }}</td><td>{{ $p->industri }}</td><td>{{ $p->lowongan_aktif_count }}</td></tr>
        @endforeach
    </table>

    <div class="footer">Dokumen ini dibuat otomatis oleh MagangKu • {{ config('app.url') }}</div>
</body>
</html>
