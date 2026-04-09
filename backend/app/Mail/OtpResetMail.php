<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OtpResetMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public string $otp) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🔐 Code de réinitialisation — AntiGaspiCI',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.otp-reset',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
