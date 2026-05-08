<?php

namespace App\Mail;

use App\Models\Reservation;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReservationAccepteeMail extends Mailable
{
    use SerializesModels;

    public function __construct(public Reservation $reservation) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[AntiGaspi CI] ✅ Votre réservation a été acceptée !',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.reservation-acceptee',
        );
    }
}
