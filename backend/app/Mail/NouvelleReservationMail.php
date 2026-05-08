<?php

namespace App\Mail;

use App\Models\Reservation;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NouvelleReservationMail extends Mailable
{
    use SerializesModels;

    public function __construct(public Reservation $reservation) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[AntiGaspi CI] Nouvelle réservation sur votre annonce',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.nouvelle-reservation',
        );
    }
}
