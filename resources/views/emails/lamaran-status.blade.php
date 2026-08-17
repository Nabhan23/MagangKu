<x-emails.layout :title="'Status Lamaran Diperbarui - MagangKu'">
    <h2 style="margin:0 0 16px;font-size:20px;color:#1E3A5F;">Halo, {{ $lamaran->pelamar->nama }} 👋</h2>

    <p style="margin:0 0 20px;font-size:14px;line-height:1.7;color:#40536B;">
        Status lamaran Anda untuk posisi <strong>{{ $lamaran->lowongan->posisi }}</strong>
        di <strong>{{ $lamaran->lowongan->perusahaan->nama_perusahaan }}</strong> telah diperbarui.
    </p>

    {{-- Perubahan status --}}
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#F5F7FA;border-radius:12px;padding:20px;margin:0 0 24px;">
        <tr>
            <td align="center" style="font-size:13px;color:#8A9BB0;padding-bottom:12px;">Status terbaru lamaran Anda</td>
        </tr>
        <tr>
            <td align="center" style="font-size:26px;font-weight:bold;color:#4FC3F7;padding-bottom:8px;">
                {{ $lamaran->status_label }}
            </td>
        </tr>
        <tr>
            <td align="center" style="font-size:13px;color:#8A9BB0;">
                Sebelumnya: <s>{{ \App\Models\Lamaran::STATUS[$oldStatus][0] ?? $oldStatus }}</s>
            </td>
        </tr>
    </table>

    {{-- Detail lamaran --}}
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin:0 0 24px;font-size:13px;color:#40536B;">
        <tr>
            <td style="padding:6px 0;color:#8A9BB0;width:40%;">Posisi</td>
            <td style="padding:6px 0;"><strong>{{ $lamaran->lowongan->posisi }}</strong></td>
        </tr>
        <tr>
            <td style="padding:6px 0;color:#8A9BB0;">Perusahaan</td>
            <td style="padding:6px 0;"><strong>{{ $lamaran->lowongan->perusahaan->nama_perusahaan }}</strong></td>
        </tr>
        <tr>
            <td style="padding:6px 0;color:#8A9BB0;">Lokasi</td>
            <td style="padding:6px 0;">{{ $lamaran->lowongan->lokasi }}</td>
        </tr>
        <tr>
            <td style="padding:6px 0;color:#8A9BB0;">Tanggal melamar</td>
            <td style="padding:6px 0;">{{ $lamaran->created_at->translatedFormat('d F Y') }}</td>
        </tr>
    </table>

    @if($lamaran->status === 'diterima')
        <p style="margin:0 0 20px;font-size:14px;line-height:1.7;color:#40536B;">
            🎉 <strong>Selamat!</strong> Perusahaan akan segera menghubungi Anda untuk langkah selanjutnya.
        </p>
    @elseif($lamaran->status === 'ditolak')
        <p style="margin:0 0 20px;font-size:14px;line-height:1.7;color:#40536B;">
            Jangan berkecil hati! Terus tingkatkan diri dan coba lamaran lainnya — masih banyak peluang menantimu di MagangKu.
        </p>
    @else
        <p style="margin:0 0 20px;font-size:14px;line-height:1.7;color:#40536B;">
            Pantau terus perkembangan lamaran Anda melalui fitur tracking lamaran.
        </p>
    @endif

    <table role="presentation" cellpadding="0" cellspacing="0">
        <tr>
            <td style="background:linear-gradient(135deg,#4FC3F7,#0288D1);border-radius:10px;">
                <a href="{{ route('pelamar.lamaran.show', $lamaran) }}" style="display:inline-block;padding:12px 28px;color:#FFFFFF;font-size:14px;font-weight:bold;text-decoration:none;">Lihat Detail Lamaran</a>
            </td>
        </tr>
    </table>
</x-emails.layout>
