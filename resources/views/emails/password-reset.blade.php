<x-emails.layout :title="'Reset Password - MagangKu'">
    <h2 style="margin:0 0 16px;font-size:20px;color:#1E3A5F;">Reset Password Anda</h2>

    <p style="margin:0 0 20px;font-size:14px;line-height:1.7;color:#40536B;">
        Kami menerima permintaan untuk mengatur ulang password akun MagangKu Anda.
        Klik tombol di bawah untuk memilih password baru:
    </p>

    <table role="presentation" cellpadding="0" cellspacing="0">
        <tr>
            <td style="background:linear-gradient(135deg,#4FC3F7,#0288D1);border-radius:10px;">
                <a href="{{ route('password.reset', $token) }}" style="display:inline-block;padding:12px 28px;color:#FFFFFF;font-size:14px;font-weight:bold;text-decoration:none;">Reset Password</a>
            </td>
        </tr>
    </table>

    <p style="margin:20px 0 0;font-size:12px;line-height:1.6;color:#8A9BB0;">
        Tautan ini berlaku selama {{ $expiresMinutes }} menit dan hanya bisa digunakan satu kali.
        Jika Anda tidak meminta reset password, abaikan email ini — password Anda tidak berubah.
    </p>
</x-emails.layout>
