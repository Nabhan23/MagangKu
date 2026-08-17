<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'MagangKu' }}</title>
</head>
<body style="margin:0;padding:0;background-color:#F5F7FA;font-family:Arial,Helvetica,sans-serif;color:#1E3A5F;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#F5F7FA;padding:24px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;background-color:#FFFFFF;border-radius:16px;overflow:hidden;box-shadow:0 4px 24px rgba(30,58,95,.08);">

                    {{-- Header --}}
                    <tr>
                        <td style="background:linear-gradient(135deg,#4FC3F7,#0288D1);padding:28px 32px;text-align:center;">
                            <div style="font-size:26px;font-weight:bold;color:#FFFFFF;letter-spacing:2px;">MAGANG<span style="color:#B3E5FC;">KU</span></div>
                            <div style="font-size:12px;color:rgba(255,255,255,.9);margin-top:4px;">Langkah Awal Menuju Karier Impian</div>
                        </td>
                    </tr>

                    {{-- Body --}}
                    <tr>
                        <td style="padding:32px;">
                            {{ $slot }}
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="padding:20px 32px;border-top:1px solid #EEF2F7;text-align:center;font-size:12px;color:#8A9BB0;">
                            <div style="font-weight:bold;color:#1E3A5F;margin-bottom:6px;">MagangKu</div>
                            <div>Platform rekrutmen &amp; magang terpercaya di Indonesia</div>
                            <div style="margin-top:8px;">
                                <a href="{{ route('home') }}" style="color:#4FC3F7;text-decoration:none;margin:0 8px;">Beranda</a>
                                <a href="{{ route('jobs.index') }}" style="color:#4FC3F7;text-decoration:none;margin:0 8px;">Lowongan</a>
                                <a href="{{ route('contact') }}" style="color:#4FC3F7;text-decoration:none;margin:0 8px;">Kontak</a>
                            </div>
                            <div style="margin-top:8px;">© {{ date('Y') }} MagangKu. Semua hak dilindungi.</div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
