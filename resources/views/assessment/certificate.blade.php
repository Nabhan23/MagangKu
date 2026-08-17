<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sertifikat - MagangKu</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Georgia', 'Times New Roman', serif;
            background: #E3F2FD;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 30px;
        }
        .cert {
            width: 100%;
            max-width: 900px;
            background: #fff;
            border-radius: 16px;
            padding: 50px 60px;
            position: relative;
            box-shadow: 0 24px 60px rgba(30, 58, 95, .18);
            border: 8px double #4FC3F7;
        }
        .cert::before {
            content: '';
            position: absolute;
            inset: 12px;
            border: 1px solid #B3E5FC;
            border-radius: 10px;
            pointer-events: none;
        }
        .ribbon {
            text-align: center;
            letter-spacing: 4px;
            font-weight: 700;
            color: #0288D1;
            font-size: 13px;
            text-transform: uppercase;
            margin-bottom: 6px;
        }
        h1 {
            text-align: center;
            font-size: 42px;
            color: #1E3A5F;
            letter-spacing: 6px;
            margin-bottom: 2px;
        }
        h1 span { color: #4FC3F7; }
        .tagline {
            text-align: center;
            color: #7a8aa0;
            font-size: 13px;
            font-style: italic;
            margin-bottom: 34px;
        }
        .title {
            text-align: center;
            font-size: 26px;
            color: #1E3A5F;
            text-transform: uppercase;
            letter-spacing: 8px;
            margin-bottom: 26px;
        }
        .given {
            text-align: center;
            color: #6b7c93;
            font-size: 15px;
            margin-bottom: 8px;
        }
        .name {
            text-align: center;
            font-size: 34px;
            font-weight: 700;
            color: #0288D1;
            border-bottom: 2px solid #B3E5FC;
            padding-bottom: 10px;
            margin: 0 40px 24px;
        }
        .body {
            text-align: center;
            color: #40536B;
            font-size: 15px;
            line-height: 1.9;
            max-width: 640px;
            margin: 0 auto;
        }
        .body .test { font-weight: 700; color: #1E3A5F; }
        .score {
            display: inline-block;
            margin: 22px auto 8px;
            background: linear-gradient(135deg, #4FC3F7, #0288D1);
            color: #fff;
            font-weight: 700;
            font-size: 22px;
            padding: 10px 34px;
            border-radius: 50px;
        }
        .meta {
            display: flex;
            justify-content: space-between;
            margin-top: 44px;
            padding-top: 18px;
            border-top: 1px dashed #dbe4ee;
            font-size: 12px;
            color: #7a8aa0;
        }
        .meta .right { text-align: right; }
        .meta b { color: #1E3A5F; display: block; font-size: 13px; }
        .no-sert { text-align: center; margin-top: 18px; font-size: 12px; color: #9aa4b2; letter-spacing: 1px; }
        .btn-print {
            margin-top: 24px;
            padding: 13px 40px;
            background: linear-gradient(135deg, #4FC3F7, #0288D1);
            color: #fff;
            border: 0;
            border-radius: 12px;
            font-weight: 700;
            font-size: 14px;
            cursor: pointer;
            font-family: 'Segoe UI', Arial, sans-serif;
        }
        @media print {
            body { background: #fff; padding: 0; }
            .cert { box-shadow: none; }
            .btn-print { display: none; }
            @page { size: landscape; margin: 0; }
        }
    </style>
</head>
<body>
    <div class="cert">
        <div class="ribbon">Sertifikat Kelulusan</div>
        <h1>MAGANG<span>KU</span></h1>
        <div class="tagline">"Langkah Awal Menuju Karier Impian"</div>

        <div class="title">Skill Assessment</div>

        <div class="given">Diberikan kepada</div>
        <div class="name">{{ $result->user->name }}</div>

        <div class="body">
            telah berhasil menyelesaikan tes
            <div class="test" style="font-size:20px;margin:8px 0;">{{ $result->assessment->judul }}</div>
            dengan skor
            <div class="score">{{ $result->skor }}</div>
            <div style="font-size:13px;color:#7a8aa0;">({{ $result->benar }} benar dari {{ $result->total_soal }} soal)</div>
        </div>

        <div class="meta">
            <div class="left">
                <b>Tanggal</b>
                {{ $result->created_at->translatedFormat('d F Y') }}
            </div>
            <div class="right">
                <b>Admin MagangKu</b>
                <i>Tim MagangKu</i>
            </div>
        </div>

        <div class="no-sert">Nomor Sertifikat: <b>{{ $result->no_sertifikat }}</b></div>
    </div>

    <button class="btn-print" onclick="window.print()">🖨️ Unduh / Cetak Sertifikat</button>
</body>
</html>
