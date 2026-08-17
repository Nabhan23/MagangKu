<x-emails.layout :title="'Lowongan Baru - MagangKu'">
    <h2 style="margin:0 0 16px;font-size:20px;color:#1E3A5F;">Halo! Ada lowongan baru untuk Anda 👋</h2>

    <p style="margin:0 0 20px;font-size:14px;line-height:1.7;color:#40536B;">
        Perusahaan yang Anda ikuti baru saja memposting lowongan:
        <strong>{{ $lowongan->perusahaan->nama_perusahaan }}</strong>
    </p>

    {{-- Detail lowongan --}}
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#F5F7FA;border-radius:12px;padding:20px;margin:0 0 24px;font-size:13px;color:#40536B;">
        <tr>
            <td style="padding:6px 0;color:#8A9BB0;width:40%;">Posisi</td>
            <td style="padding:6px 0;"><strong style="font-size:16px;color:#1E3A5F;">{{ $lowongan->posisi }}</strong></td>
        </tr>
        <tr>
            <td style="padding:6px 0;color:#8A9BB0;">Jenis</td>
            <td style="padding:6px 0;"><strong>{{ $lowongan->jenis_label }}</strong></td>
        </tr>
        <tr>
            <td style="padding:6px 0;color:#8A9BB0;">Lokasi</td>
            <td style="padding:6px 0;"><strong>{{ $lowongan->lokasi ?: 'Remote' }}</strong></td>
        </tr>
        <tr>
            <td style="padding:6px 0;color:#8A9BB0;">Gaji</td>
            <td style="padding:6px 0;"><strong>Rp {{ number_format($lowongan->gaji_min, 0, ',', '.') }} - Rp {{ number_format($lowongan->gaji_max, 0, ',', '.') }}</strong></td>
        </tr>
        <tr>
            <td style="padding:6px 0;color:#8A9BB0;">Deadline</td>
            <td style="padding:6px 0;"><strong>{{ $lowongan->deadline->translatedFormat('d F Y') }}</strong></td>
        </tr>
    </table>

    <table role="presentation" cellpadding="0" cellspacing="0">
        <tr>
            <td style="background:linear-gradient(135deg,#4FC3F7,#0288D1);border-radius:10px;">
                <a href="{{ route('jobs.show', $lowongan) }}" style="display:inline-block;padding:12px 28px;color:#FFFFFF;font-size:14px;font-weight:bold;text-decoration:none;">Lihat & Lamar Sekarang</a>
            </td>
        </tr>
    </table>

    <p style="margin:20px 0 0;font-size:12px;line-height:1.6;color:#8A9BB0;">
        Anda menerima email ini karena mengikuti {{ $lowongan->perusahaan->nama_perusahaan }} di MagangKu.
    </p>
</x-emails.layout>
