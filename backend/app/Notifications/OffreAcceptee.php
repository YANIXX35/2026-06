<?php

namespace App\Notifications;

use App\Models\Offre;
use Illuminate\Notifications\Notification;

class OffreAcceptee extends Notification
{
    public function __construct(public Offre $offre) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'       => 'offre_acceptee',
            'icone'      => '✅',
            'titre'      => 'Votre offre a été acceptée',
            'message'    => 'Votre offre à ' . number_format($this->offre->prix_propose, 0, ',', ' ') . ' FCFA pour « ' . $this->offre->annonce->titre . ' » a été acceptée. Une réservation a été créée.',
            'url'        => route('reservations.mes-reservations'),
            'annonce_id' => $this->offre->annonce_id,
        ];
    }
}
