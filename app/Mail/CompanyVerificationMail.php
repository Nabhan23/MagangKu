<?php

namespace App\Mail;

use App\Models\PerusahaanProfile;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CompanyVerificationMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param string $status 'verified' | 'rejected'
     */
    public function __construct(
        public PerusahaanProfile $perusahaan,
        public string $status,
        public ?string $pesan = null,
    ) {}

    public function envelope(): Envelope
    {
        $subject = $this->status === 'verified'
            ? 'Perusahaan Terverifikasi 🎉 - MagangKu'
            : 'Verifikasi Perusahaan Ditolak - MagangKu';

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.company-verification',
        );
    }
}
