<?php

namespace App\Notifications;

use App\Models\Reservation;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Bus\Queueable;

class ReservationAcceptee extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Reservation $reservation) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'        => 'reservation_acceptee',
            'icone'       => '✅',
            'titre'       => 'Réservation acceptée',
            'message'     => 'Votre réservation pour « ' . $this->reservation->annonce->titre . ' » a été acceptée.',
            'url'         => route('reservations.mes-reservations'),
            'annonce_id'  => $this->reservation->annonce_id,
        ];
    }
}
