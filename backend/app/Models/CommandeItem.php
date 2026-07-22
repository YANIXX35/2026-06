<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommandeItem extends Model
{
    protected $fillable = [
        'commande_id', 'annonce_id', 'fournisseur_id', 'quantite',
        'prix_unitaire', 'commission_montant', 'montant_net', 'statut'
    ];

    protected $casts = [
        'quantite'           => 'decimal:2',
        'prix_unitaire'      => 'decimal:2',
        'commission_montant' => 'decimal:2',
        'montant_net'        => 'decimal:2',
    ];

    public function commande()
    {
        return $this->belongsTo(Commande::class);
    }

    public function annonce()
    {
        return $this->belongsTo(Annonce::class);
    }

    public function fournisseur()
    {
        return $this->belongsTo(User::class, 'fournisseur_id');
    }

    public function sousTotal(): float
    {
        return (float) $this->prix_unitaire * (float) $this->quantite;
    }
}
