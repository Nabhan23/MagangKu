<?php

namespace App\Mail;

use App\Models\Interview;
use App\Models\Lamaran;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InterviewScheduledMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Lamaran $lamaran,
        public Interview $interview,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Interview Dijadwalkan - MagangKu',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.interview-scheduled',
        );
    }
}
