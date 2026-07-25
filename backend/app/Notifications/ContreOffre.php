<?php

namespace App\Notifications;

use App\Models\Offre;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Bus\Queueable;

/**
 * Notifie l'acheteur qu'une contre-offre a été faite par le fournisseur.
 * Utilisée uniquement quand est_contre_offre = true.
 */
class ContreOffre extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Offre $offre) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $prixPropose = number_format($this->offre->prix_propose, 0, ',', ' ');
        $titreAnnonce = $this->offre->annonce->titre ?? 'annonce';
        $fournisseur  = $this->offre->fournisseur->prenom ?? 'Le fournisseur';

        return [
            'type'       => 'contre_offre',
            'icone'      => '🔄',
            'titre'      => 'Contre-offre reçue',
            'message'    => $fournisseur . ' vous propose ' . $prixPropose . ' FCFA pour « ' . $titreAnnonce . ' ». Acceptez ou refusez dans la messagerie.',
            'url'        => route('messages.show', $this->offre->conversation_id),
            'annonce_id' => $this->offre->annonce_id,
        ];
    }
}
