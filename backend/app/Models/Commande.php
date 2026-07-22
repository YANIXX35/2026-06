<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Commande extends Model
{
    protected $fillable = [
        'user_id', 'statut', 'montant_total', 'commission_taux', 'montant_commission',
        'montant_net_fournisseur', 'adresse_livraison', 'message',
        'mode_paiement', 'telephone_paiement', 'reference_paiement', 'statut_paiement',
    ];

    protected $casts = [
        'montant_total'           => 'decimal:2',
        'commission_taux'         => 'decimal:4',
        'montant_commission'      => 'decimal:2',
        'montant_net_fournisseur' => 'decimal:2',
    ];

    public function acheteur()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function items()
    {
        return $this->hasMany(CommandeItem::class);
    }
}
