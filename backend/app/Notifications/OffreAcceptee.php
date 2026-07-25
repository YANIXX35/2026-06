<?php

namespace App\Notifications;

use App\Models\Offre;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Bus\Queueable;

class OffreAcceptee extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Offre $offre) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $prixPropose  = number_format($this->offre->prix_propose, 0, ',', ' ');
        $titreAnnonce = $this->offre->annonce->titre ?? 'annonce';

        $urlCible = $this->offre->annonce ? route('annonces.show', $this->offre->annonce) : url('/');

        return [
            'type'        => 'offre_acceptee',
            'icone'       => '✅',
            'titre'       => 'Votre offre a été acceptée !',
            'message'     => 'Votre offre à ' . $prixPropose . ' FCFA pour « ' . $titreAnnonce . ' » a été acceptée. Ajoutez l\'article à votre panier pour finaliser l\'achat.',
            'url'         => $urlCible,
            'annonce_id'  => $this->offre->annonce_id,
            'cta'         => 'Ajouter au panier',
        ];
    }
}

