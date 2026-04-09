<?php

namespace App\Notifications;

use App\Models\Reservation;
use Illuminate\Notifications\Notification;

class ReservationCompletee extends Notification
{
    public function __construct(public Reservation $reservation) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'       => 'reservation_completee',
            'icone'      => '🎉',
            'titre'      => 'Transaction terminée',
            'message'    => 'La transaction pour « ' . $this->reservation->annonce->titre . ' » est complétée. Vous pouvez laisser un avis !',
            'url'        => route('reservations.mes-reservations'),
            'annonce_id' => $this->reservation->annonce_id,
        ];
    }
}
