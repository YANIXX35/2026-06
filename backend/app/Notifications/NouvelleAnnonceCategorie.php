<?php

namespace App\Notifications;

use App\Models\Annonce;
use Illuminate\Notifications\Notification;

class NouvelleAnnonceCategorie extends Notification
{
    public function __construct(public Annonce $annonce) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $prix = $this->annonce->type_offre === 'don'
            ? 'GRATUIT'
            : number_format($this->annonce->prix, 0, ',', ' ') . ' FCFA';

        return [
            'titre'   => 'Nouvelle annonce dans ' . ($this->annonce->categorie->nom ?? 'votre catégorie'),
            'message' => $this->annonce->titre . ' — ' . $prix,
            'url'     => route('annonces.show', $this->annonce),
            'icone'   => $this->annonce->categorie->icone ?? '🔔',
            'urgent'  => $this->annonce->estUrgent(),
        ];
    }
}
