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

    public function sousTotal(): float
    {
        $prix = $this->annonce->type_offre === 'don' ? 0 : (float) $this->annonce->prix;
        return $prix * (float) $this->quantite;
    }
}
