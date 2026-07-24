<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    protected $fillable = ['user_id', 'annonce_id', 'quantite'];

    protected $casts = ['quantite' => 'decimal:2'];

    public function annonce()
    {
        return $this->belongsTo(Annonce::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Récupère le prix unitaire applicable pour cet item dans le panier.
     * Si une réservation avec prix négocié existe et est acceptée, on l'utilise.
     * Sinon, c'est le prix public de l'annonce.
     */
    public function prixUnitaire(): float
    {
        if ($this->annonce->type_offre === 'don') {
            return 0;
        }

        // Vérifier si l'utilisateur a une réservation acceptée (négociation) pour cette annonce
        $reservation = \App\Models\Reservation::where('user_id', $this->user_id)
            ->where('annonce_id', $this->annonce_id)
            ->where('statut', 'acceptée')
            ->whereNotNull('prix_negocie')
            ->first();

        if ($reservation) {
            return (float) $reservation->prix_negocie;
        }

        return (float) $this->annonce->prix;
    }

    public function sousTotal(): float
    {
        return $this->prixUnitaire() * (float) $this->quantite;
    }
}
