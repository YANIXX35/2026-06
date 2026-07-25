<?php

namespace App\Notifications;

use App\Models\Offre;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Bus\Queueable;

class OffrePrixMaintenu extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Offre $offre) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $prixPublic = number_format($this->offre->annonce->prix, 0, ',', ' ');
        $titreAnnonce = $this->offre->annonce->titre ?? 'annonce';

        return [
            'type'       => 'offre_prix_maintenu',
            'icone'      => '📌',
            'titre'      => 'Prix initial maintenu',
            'message'    => 'Le fournisseur a choisi de maintenir le prix initial de ' . $prixPublic . ' FCFA pour « ' . $titreAnnonce . ' ». Aucune réduction n\'a été appliquée.',
            'url'        => route('messages.show', $this->offre->conversation_id),
            'annonce_id' => $this->offre->annonce_id,
        ];
    }
}
