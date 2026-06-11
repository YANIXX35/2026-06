<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OtpInscriptionMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public string $otp, public string $prenom) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '✅ Confirmez votre email — AntiGaspiCI',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.otp-inscription',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
