<?php

namespace App\Mail;

use App\Models\Lamaran;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LamaranStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param string $oldStatus Status sebelum perubahan (key Lamaran::STATUS)
     */
    public function __construct(
        public Lamaran $lamaran,
        public string $oldStatus,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Status Lamaran Diperbarui - MagangKu',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.lamaran-status',
        );
    }
}
