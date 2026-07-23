<?php

namespace App\Notifications;

use App\Models\Offre;
use Illuminate\Notifications\Notification;

class OffreRefusee extends Notification
{
    public function __construct(public Offre $offre) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'       => 'offre_refusee',
            'icone'      => '❌',
            'titre'      => 'Offre refusée',
            'message'    => 'Votre offre à ' . number_format($this->offre->prix_propose, 0, ',', ' ') . ' FCFA pour « ' . $this->offre->annonce->titre . ' » a été refusée.',
            'url'        => route('messages.show', $this->offre->conversation_id),
            'annonce_id' => $this->offre->annonce_id,
        ];
    }
}
