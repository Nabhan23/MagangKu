<?php

namespace App\Mail;

use App\Models\CompanyFeedback;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CompanyFeedbackMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public CompanyFeedback $feedback)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Feedback dari Perusahaan - MagangKu',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.company-feedback',
        );
    }
}
