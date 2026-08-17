<x-emails.layout :title="'Verifikasi Perusahaan - MagangKu'">
    <h2 style="margin:0 0 16px;font-size:20px;color:#1E3A5F;">
        Halo, Tim {{ $perusahaan->nama_perusahaan }} 👋
    </h2>

    @if($status === 'verified')
        <p style="margin:0 0 20px;font-size:14px;line-height:1.7;color:#40536B;">
            <strong style="color:#22C55E;">Selamat! 🎉</strong> Perusahaan
            <strong>{{ $perusahaan->nama_perusahaan }}</strong> telah <strong>terverifikasi</strong>
            oleh admin MagangKu.
        </p>

        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#F5F7FA;border-radius:12px;padding:20px;margin:0 0 24px;">
            <tr>
                <td align="center" style="font-size:26px;">✅</td>
            </tr>
            <tr>
                <td align="center" style="font-size:15px;font-weight:bold;color:#1E3A5F;padding:8px 0 4px;">Perusahaan Terverifikasi</td>
            </tr>
            <tr>
                <td align="center" style="font-size:13px;color:#40536B;line-height:1.6;">
                    Anda kini bisa memposting lowongan dan magang.<br>
                    Lowongan terverifikasi lebih dipercaya oleh pelamar.
                </td>
            </tr>
        </table>

        <table role="presentation" cellpadding="0" cellspacing="0">
            <tr>
                <td style="background:linear-gradient(135deg,#4FC3F7,#0288D1);border-radius:10px;">
                    <a href="{{ route('perusahaan.dashboard') }}" style="display:inline-block;padding:12px 28px;color:#FFFFFF;font-size:14px;font-weight:bold;text-decoration:none;">Buka Dashboard HRD</a>
                </td>
            </tr>
        </table>
    @else
        <p style="margin:0 0 20px;font-size:14px;line-height:1.7;color:#40536B;">
            Kami mohon maaf, verifikasi perusahaan
            <strong>{{ $perusahaan->nama_perusahaan }}</strong> <strong style="color:#EF4444;">ditolak</strong> oleh admin MagangKu.
        </p>

        @if($pesan)
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#FEF2F2;border-radius:12px;padding:16px 20px;margin:0 0 24px;border-left:4px solid #EF4444;">
                <tr>
                    <td style="font-size:13px;color:#991B1B;line-height:1.6;">
                        <strong>Alasan:</strong> {{ $pesan }}
                    </td>
                </tr>
            </table>
        @else
            <p style="margin:0 0 20px;font-size:14px;line-height:1.7;color:#40536B;">
                Silakan perbaiki dokumen verifikasi dan kirim ulang, atau hubungi admin untuk informasi lebih lanjut.
            </p>
        @endif

        <table role="presentation" cellpadding="0" cellspacing="0">
            <tr>
                <td style="background:linear-gradient(135deg,#4FC3F7,#0288D1);border-radius:10px;">
                    <a href="{{ route('perusahaan.profile.edit') }}" style="display:inline-block;padding:12px 28px;color:#FFFFFF;font-size:14px;font-weight:bold;text-decoration:none;">Kirim Ulang Dokumen</a>
                </td>
            </tr>
        </table>
    @endif

    <p style="margin:24px 0 0;font-size:12px;color:#8A9BB0;line-height:1.6;">
        Jika Anda memiliki pertanyaan, silakan hubungi kami melalui halaman <a href="{{ route('contact') }}" style="color:#4FC3F7;">Kontak</a>.
    </p>
</x-emails.layout>
