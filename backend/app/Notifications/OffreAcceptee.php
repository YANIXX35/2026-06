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

        // Trouver la réservation créée lors de l'acceptation pour pointer
        // directement vers la page de paiement AU PRIX NÉGOCIÉ.
        $reservation  = $this->offre->annonce
            ?->reservations()
            ->where('user_id', $this->offre->acheteur_id)
            ->where('offre_id', $this->offre->id)
            ->latest()
            ->first();

        // Si on a la réservation → parcours négocié (prix réduit)
        // Sinon → repli sur la liste des réservations
        $urlCible = $reservation
            ? route('paiement.reservation.show', $reservation)
            : route('reservations.mes-reservations');

        return [
            'type'        => 'offre_acceptee',
            'icone'       => '✅',
            'titre'       => 'Votre offre a été acceptée !',
            'message'     => 'Votre offre à ' . $prixPropose . ' FCFA pour « ' . $titreAnnonce . ' » a été acceptée. Finalisez votre achat au prix négocié.',
            'url'         => $urlCible,
            'annonce_id'  => $this->offre->annonce_id,
            'cta'         => 'Payer ' . $prixPropose . ' FCFA',
        ];
    }
}

