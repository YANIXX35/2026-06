<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    protected $fillable = [
        'annonce_id',
        'user_id',
        'quantite_demandee',
        'message',
        'statut',
        'date_collecte_souhaitee',
    ];

    protected $casts = [
        'date_collecte_souhaitee' => 'datetime',
        'quantite_demandee'       => 'decimal:2',
    ];

    public function annonce()
    {
        return $this->belongsTo(Annonce::class);
    }

    public function acheteur()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function avis()
    {
        return $this->hasOne(Avis::class);
    }
}
