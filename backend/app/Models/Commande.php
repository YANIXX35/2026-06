<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Commande extends Model
{
    protected $fillable = ['user_id', 'statut', 'montant_total', 'adresse_livraison', 'message'];

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
