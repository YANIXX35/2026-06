<?php

namespace App\Notifications;

use App\Models\Offre;
use Illuminate\Notifications\Notification;

class NouvelleOffre extends Notification
{
    public function __construct(public Offre $offre) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'       => 'nouvelle_offre',
            'icone'      => '💬',
            'titre'      => 'Nouvelle offre de prix',
            'message'    => $this->offre->acheteur->prenom . ' propose ' . number_format($this->offre->prix_propose, 0, ',', ' ') . ' FCFA pour « ' . $this->offre->annonce->titre . ' ».',
            'url'        => route('messages.show', $this->offre->conversation_id),
            'annonce_id' => $this->offre->annonce_id,
        ];
    }
}
