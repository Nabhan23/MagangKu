<x-emails.layout :title="'Feedback dari Perusahaan - MagangKu'">
    <h2 style="margin:0 0 16px;font-size:20px;color:#1E3A5F;">Halo, {{ $feedback->lamaran->pelamar->nama }} 👋</h2>

    <p style="margin:0 0 20px;font-size:14px;line-height:1.7;color:#40536B;">
        <strong>{{ $feedback->perusahaan->nama_perusahaan }}</strong> telah memberikan
        <strong>feedback</strong> untuk lamaran Anda pada posisi
        <strong>{{ $feedback->lamaran->lowongan->posisi }}</strong>.
    </p>

    {{-- Rating --}}
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#F5F7FA;border-radius:12px;padding:20px;margin:0 0 24px;">
        <tr>
            <td align="center" style="font-size:13px;color:#8A9BB0;padding-bottom:10px;">Penilaian perusahaan terhadap Anda</td>
        </tr>
        <tr>
            <td align="center" style="font-size:24px;letter-spacing:3px;color:#F59E0B;padding-bottom:12px;">
                {!! str_repeat('★', $feedback->rating) !!}{!! str_repeat('☆', 5 - $feedback->rating) !!}
            </td>
        </tr>
    </table>

    @if($feedback->feedback)
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin:0 0 24px;font-size:14px;line-height:1.7;color:#40536B;">
            <tr>
                <td style="padding:6px 0;color:#8A9BB0;">Feedback perusahaan</td>
            </tr>
            <tr>
                <td style="padding:6px 0;background:#F5F7FA;border-radius:10px;padding:16px;font-style:italic;">
                    {{ $feedback->feedback }}
                </td>
            </tr>
        </table>
    @endif

    <p style="margin:0 0 20px;font-size:14px;line-height:1.7;color:#40536B;">
        Terus kembangkan diri Anda — kesempatan berikutnya menanti di MagangKu. 🚀
    </p>

    <table role="presentation" cellpadding="0" cellspacing="0">
        <tr>
            <td style="background:linear-gradient(135deg,#4FC3F7,#0288D1);border-radius:10px;">
                <a href="{{ route('pelamar.lamaran.show', $feedback->lamaran_id) }}" style="display:inline-block;padding:12px 28px;color:#FFFFFF;font-size:14px;font-weight:bold;text-decoration:none;">Lihat Detail Lamaran</a>
            </td>
        </tr>
    </table>
</x-emails.layout>
