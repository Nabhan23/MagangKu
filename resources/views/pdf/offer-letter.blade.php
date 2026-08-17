<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Surat Penerimaan - {{ $lamaran->lowongan->perusahaan->nama_perusahaan }}</title>
    <style>
        * { font-family: 'DejaVu Sans', sans-serif; }
        body { font-size: 12px; color: #1f2937; line-height: 1.6; margin: 0; padding: 0; }
        .kop { border-bottom: 3px solid #0EA5E9; padding-bottom: 14px; margin-bottom: 24px; }
        .kop h1 { margin: 0; font-size: 20px; color: #0c4a6e; }
        .kop p { margin: 2px 0; font-size: 10px; color: #475569; }
        .meta { margin-bottom: 24px; font-size: 11px; }
        .meta table { width: 100%; }
        .meta td { padding: 1px 0; }
        .judul { text-align: center; font-size: 15px; font-weight: bold; text-decoration: underline; margin-bottom: 28px; }
        .isi { text-align: justify; }
        .isi p { margin: 8px 0; }
        .ttd { margin-top: 48px; }
        .ttd .nama { font-weight: bold; margin-top: 72px; }
    </style>
</head>
<body>
    <div class="kop">
        <h1>{{ $lamaran->lowongan->perusahaan->nama_perusahaan }}</h1>
        <p>{{ $lamaran->lowongan->perusahaan->alamat_lengkap ?: $lamaran->lowongan->perusahaan->lokasi }}</p>
        @if($lamaran->lowongan->perusahaan->telepon)
            <p>Telp: {{ $lamaran->lowongan->perusahaan->telepon }}@if($lamaran->lowongan->perusahaan->email_kontak) &nbsp;|&nbsp; Email: {{ $lamaran->lowongan->perusahaan->email_kontak }}@endif</p>
        @elseif($lamaran->lowongan->perusahaan->email_kontak)
            <p>Email: {{ $lamaran->lowongan->perusahaan->email_kontak }}</p>
        @endif
    </div>

    <div class="meta">
        <table>
            <tr><td style="width:130px;">Nomor</td><td>: {{ $nomor }}</td></tr>
            <tr><td>Lampiran</td><td>: -</td></tr>
            <tr><td>Perihal</td><td>: <b>Surat Penerimaan Magang</b></td></tr>
        </table>
        <p style="text-align:right;margin-top:8px;">{{ $tanggal }}</p>
    </div>

    <div class="judul">SURAT PENERIMAAN MAGANG</div>

    <div class="isi">
        <p>Kepada Yth.<br>
        <b>{{ $lamaran->pelamar->nama_lengkap }}</b><br>
        {{ $lamaran->pelamar->lokasi }}{{ $lamaran->pelamar->alamat ? ', ' . $lamaran->pelamar->alamat : '' }}</p>

        <p>Dengan hormat,</p>

        <p>Berdasarkan hasil seleksi yang telah kami lakukan, bersama ini kami <b>menerima</b> Saudara/i untuk bergabung sebagai:</p>

        <p style="text-align:center;font-size:14px;"><b>{{ $lamaran->lowongan->posisi }}</b><br>
        di <b>{{ $lamaran->lowongan->perusahaan->nama_perusahaan }}</b></p>

        <p>Posisi tersebut merupakan bagian dari program magang kami dengan ketentuan yang akan dijelaskan lebih lanjut. Jadwal mulai magang, penempatan, serta ketentuan lainnya akan kami sampaikan melalui komunikasi selanjutnya.</p>

        <p>Segala keputusan dan ketentuan dalam surat ini bersifat final dan mengikat kedua belah pihak. Kami menantikan kontribusi positif Saudara/i di perusahaan kami.</p>

        <p>Demikian surat penerimaan ini kami sampaikan. Atas perhatian dan kerja samanya, kami ucapkan terima kasih.</p>
    </div>

    <div class="ttd">
        <p style="text-align:right;">Hormat kami,</p>
        <p style="text-align:right;">{{ $lamaran->lowongan->perusahaan->nama_perusahaan }}</p>
        <p style="text-align:right;margin-top:70px;"><b>{{ $lamaran->lowongan->perusahaan->nama_perusahaan }}</b></p>
    </div>
</body>
</html>
