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
        $prixPropose  = number_format($this->offre->prix_propose, 0, ',', ' ');
        $titreAnnonce = $this->offre->annonce->titre ?? 'annonce';

        // Pointer directement vers le paiement si la réservation a été créée
        // et que l'annonce est de type vente (pas de paiement pour un don).
        $urlCible = route('reservations.mes-reservations');
        if ($this->offre->annonce && $this->offre->annonce->type_offre === 'vente') {
            $urlCible = route('paiement.show');
        }

        return [
            'type'        => 'offre_acceptee',
            'icone'       => '✅',
            'titre'       => 'Votre offre a été acceptée !',
            'message'     => 'Votre offre à ' . $prixPropose . ' FCFA pour « ' . $titreAnnonce . ' » a été acceptée. Finalisez votre achat maintenant.',
            'url'         => $urlCible,
            'annonce_id'  => $this->offre->annonce_id,
            'cta'         => 'Payer maintenant',
        ];
    }
}
