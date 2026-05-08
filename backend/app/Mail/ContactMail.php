<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactMail extends Mailable
{
    use SerializesModels;

    public function __construct(
        public string $nomExpediteur,
        public string $emailExpediteur,
        public string $sujet,
        public string $contenu
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[AntiGaspi CI] ' . $this->sujet,
            replyTo: [$this->emailExpediteur],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.contact',
        );
    }
}
