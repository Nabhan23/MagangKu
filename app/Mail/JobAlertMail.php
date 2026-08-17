<?php

namespace App\Mail;

use App\Models\Lowongan;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class JobAlertMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Lowongan $lowongan)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Lowongan Baru: ' . $this->lowongan->posisi . ' - MagangKu',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.job-alert',
        );
    }
}
