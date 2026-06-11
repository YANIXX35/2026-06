<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Commande extends Model
{
    protected $fillable = [
        'user_id', 'statut', 'montant_total', 'adresse_livraison', 'message',
        'mode_paiement', 'telephone_paiement', 'reference_paiement', 'statut_paiement',
    ];

    protected $casts = ['montant_total' => 'decimal:2'];

    public function acheteur()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function items()
    {
        return $this->hasMany(CommandeItem::class);
    }
}
