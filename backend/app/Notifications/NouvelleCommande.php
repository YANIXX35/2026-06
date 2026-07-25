<?php

namespace App\Notifications;

use App\Models\Commande;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Bus\Queueable;

class NouvelleCommande extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Commande $commande,
        public array $annonces = []
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $titres = collect($this->annonces)->pluck('titre')->implode(', ');

        return [
            'type'    => 'nouvelle_commande',
            'icone'   => '🛒',
            'titre'   => 'Nouvelle commande reçue',
            'message' => 'Un acheteur vient de commander : ' . \Illuminate\Support\Str::limit($titres, 80),
            'url'     => route('dashboard'),
        ];
    }
}
