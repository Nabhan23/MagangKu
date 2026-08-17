<x-emails.layout :title="'Interview Dijadwalkan - MagangKu'">
    <h2 style="margin:0 0 16px;font-size:20px;color:#1E3A5F;">Halo, {{ $lamaran->pelamar->nama }} 👋</h2>

    <p style="margin:0 0 20px;font-size:14px;line-height:1.7;color:#40536B;">
        Kabar baik! Anda diundang untuk mengikuti <strong>interview</strong> posisi
        <strong>{{ $lamaran->lowongan->posisi }}</strong>
        di <strong>{{ $lamaran->lowongan->perusahaan->nama_perusahaan }}</strong>.
    </p>

    {{-- Detail interview --}}
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#F5F7FA;border-radius:12px;padding:20px;margin:0 0 24px;font-size:13px;color:#40536B;">
        <tr>
            <td style="padding:6px 0;color:#8A9BB0;width:40%;">Tanggal</td>
            <td style="padding:6px 0;"><strong>{{ \Carbon\Carbon::parse($interview->tanggal)->translatedFormat('l, d F Y') }}</strong></td>
        </tr>
        <tr>
            <td style="padding:6px 0;color:#8A9BB0;">Waktu</td>
            <td style="padding:6px 0;"><strong>{{ $interview->waktu }} WIB</strong></td>
        </tr>
        <tr>
            <td style="padding:6px 0;color:#8A9BB0;">Metode</td>
            <td style="padding:6px 0;">
                <strong>{{ $interview->tipe === 'online' ? 'Online' : 'Offline' }}</strong>
                @if($interview->tipe === 'online' && $interview->link)
                    <span style="color:#4FC3F7;">({{ $interview->link }})</span>
                @elseif($interview->lokasi)
                    — {{ $interview->lokasi }}
                @endif
            </td>
        </tr>
        @if($interview->catatan)
            <tr>
                <td style="padding:6px 0;color:#8A9BB0;">Catatan</td>
                <td style="padding:6px 0;">{{ $interview->catatan }}</td>
            </tr>
        @endif
    </table>

    <p style="margin:0 0 20px;font-size:14px;line-height:1.7;color:#40536B;">
        💡 Siapkan diri Anda dengan baik: pelajari profil perusahaan, siapkan portofolio/dokumen pendukung, dan datang tepat waktu.
    </p>

    <table role="presentation" cellpadding="0" cellspacing="0">
        <tr>
            <td style="background:linear-gradient(135deg,#4FC3F7,#0288D1);border-radius:10px;">
                <a href="{{ route('pelamar.lamaran.show', $lamaran) }}" style="display:inline-block;padding:12px 28px;color:#FFFFFF;font-size:14px;font-weight:bold;text-decoration:none;">Lihat Detail Interview</a>
            </td>
        </tr>
    </table>

    @if($interview->kode)
        <p style="margin:20px 0 0;font-size:13px;line-height:1.6;color:#40536B;">
            📱 <strong>QR Code Interview:</strong> simpan undangan digital Anda dan tunjukkan saat interview berlangsung.
            <a href="{{ $interview->publicUrl() }}" style="color:#0288D1;font-weight:bold;text-decoration:none;">Buka Undangan →</a>
        </p>
    @endif
</x-emails.layout>
