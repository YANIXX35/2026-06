<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Bus\Queueable;

class ContactMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

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
