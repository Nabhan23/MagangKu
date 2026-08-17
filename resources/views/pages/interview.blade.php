<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Undangan Interview - MagangKu</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: linear-gradient(135deg, #E3F2FD 0%, #FFFFFF 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }
        .card {
            background: #fff;
            border-radius: 20px;
            max-width: 520px;
            width: 100%;
            padding: 36px 32px;
            box-shadow: 0 20px 50px rgba(30, 58, 95, .15);
            border-top: 6px solid #4FC3F7;
        }
        .brand { text-align: center; margin-bottom: 20px; }
        .brand .logo { font-weight: 800; letter-spacing: 2px; color: #1E3A5F; font-size: 20px; }
        .brand .logo span { color: #4FC3F7; }
        .brand .tagline { color: #7a8aa0; font-size: 12px; margin-top: 2px; }
        .badge {
            display: inline-block; background: #E3F2FD; color: #0288D1;
            font-size: 12px; font-weight: 700; padding: 6px 14px; border-radius: 50px;
            letter-spacing: .5px; margin-bottom: 16px;
        }
        h1 { color: #1E3A5F; font-size: 22px; margin-bottom: 4px; }
        .company { color: #6b7c93; font-size: 14px; margin-bottom: 20px; }
        .detail { background: #F5F7FA; border-radius: 14px; padding: 18px; margin-bottom: 20px; }
        .detail .row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px dashed #dbe4ee; }
        .detail .row:last-child { border-bottom: 0; }
        .detail .label { color: #7a8aa0; font-size: 12px; }
        .detail .value { color: #1E3A5F; font-weight: 700; font-size: 14px; text-align: right; }
        .qr { text-align: center; margin: 20px 0 8px; }
        .qr svg { width: 180px; height: 180px; }
        .qr-note { color: #7a8aa0; font-size: 12px; margin-top: 8px; }
        .catatan { background: #FFF8E1; border-radius: 12px; padding: 12px 16px; color: #8a6d1a; font-size: 13px; margin-top: 16px; line-height: 1.6; }
        .btn-print {
            display: block; width: 100%; margin-top: 20px; padding: 12px;
            background: linear-gradient(135deg, #4FC3F7, #0288D1); color: #fff;
            border: 0; border-radius: 12px; font-weight: 700; font-size: 14px; cursor: pointer;
        }
        .kode { text-align: center; font-size: 12px; color: #9aa4b2; margin-top: 14px; }
        .kode b { color: #1E3A5F; letter-spacing: 2px; }
        @media print {
            body { background: #fff; padding: 0; }
            .card { box-shadow: none; border-top: none; }
            .btn-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="brand">
            <div class="logo">MAGANG<span>KU</span></div>
            <div class="tagline">"Langkah Awal Menuju Karier Impian"</div>
        </div>

        <div style="text-align:center;">
            <span class="badge"><i>📅</i> UNDANGAN INTERVIEW</span>
        </div>

        <h1 style="text-align:center;">{{ $interview->lamaran->lowongan->posisi }}</h1>
        <div class="company" style="text-align:center;">{{ $interview->lamaran->lowongan->perusahaan->nama_perusahaan }}</div>

        <div class="detail">
            <div class="row">
                <div class="label">Nama Pelamar</div>
                <div class="value">{{ $interview->lamaran->pelamar->nama }}</div>
            </div>
            <div class="row">
                <div class="label">Tanggal</div>
                <div class="value">{{ $interview->tanggal->translatedFormat('l, d F Y') }}</div>
            </div>
            <div class="row">
                <div class="label">Waktu</div>
                <div class="value">{{ $interview->waktu }} WIB</div>
            </div>
            <div class="row">
                <div class="label">Tipe</div>
                <div class="value">{{ $interview->tipe === 'online' ? 'Online' : 'Offline' }}</div>
            </div>
            @if($interview->tipe === 'online' && $interview->link)
                <div class="row">
                    <div class="label">Link Meeting</div>
                    <div class="value" style="font-size:12px;word-break:break-all;">{{ $interview->link }}</div>
                </div>
            @elseif($interview->lokasi)
                <div class="row">
                    <div class="label">Lokasi</div>
                    <div class="value" style="font-size:12px;">{{ $interview->lokasi }}</div>
                </div>
            @endif
        </div>

        @if($interview->catatan)
            <div class="catatan"><b>Catatan:</b> {{ $interview->catatan }}</div>
        @endif

        <div class="qr">
            {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(180)->generate($interview->publicUrl()) !!}
        </div>
        <div class="qr-note">Scan QR ini untuk membuka detail interview</div>
        <div class="kode">KODE INTERVIEW: <b>{{ $interview->kode }}</b></div>

        <button class="btn-print" onclick="window.print()">🖨️ Cetak Undangan Ini</button>
    </div>
</body>
</html>
