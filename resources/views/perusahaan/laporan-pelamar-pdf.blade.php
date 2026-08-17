<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Daftar Pelamar - {{ $lowongan->posisi }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #1E3A5F; }
        h1 { font-size: 18px; color: #0288D1; margin: 0 0 2px; }
        .sub { color: #6c757d; margin-bottom: 14px; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #d0d7de; padding: 5px 7px; text-align: left; }
        th { background: #E3F2FD; }
        .footer { margin-top: 18px; color: #9aa4b2; font-size: 8px; text-align: center; }
    </style>
</head>
<body>
    <h1>Daftar Pelamar — {{ $lowongan->posisi }}</h1>
    <div class="sub">{{ $lowongan->perusahaan->nama_perusahaan }} • Dibuat {{ now()->translatedFormat('d F Y H:i') }} • {{ $lamarans->count() }} pelamar</div>

    <table>
        <tr>
            <th>No</th>
            <th>Nama</th>
            <th>Email</th>
            <th>Universitas</th>
            <th>Jurusan</th>
            <th>Lokasi</th>
            <th>Status</th>
            <th>Tanggal Lamar</th>
        </tr>
        @foreach($lamarans as $i => $lamaran)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $lamaran->pelamar->nama }}</td>
                <td>{{ $lamaran->pelamar->user->email }}</td>
                <td>{{ $lamaran->pelamar->universitas }}</td>
                <td>{{ $lamaran->pelamar->jurusan }}</td>
                <td>{{ $lamaran->pelamar->lokasi }}</td>
                <td>{{ $lamaran->status_label }}</td>
                <td>{{ $lamaran->created_at->format('d M Y') }}</td>
            </tr>
        @endforeach
    </table>

    <div class="footer">Dokumen ini dibuat otomatis oleh MagangKu</div>
</body>
</html>
