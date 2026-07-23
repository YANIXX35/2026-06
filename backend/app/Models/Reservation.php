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
        'prix_negocie',
        'offre_id',
    ];

    protected $casts = [
        'date_collecte_souhaitee' => 'datetime',
        'quantite_demandee'       => 'decimal:2',
        'prix_negocie'            => 'decimal:2',
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

    public function offre()
    {
        return $this->belongsTo(Offre::class);
    }

    /**
     * Prix reellement du pour cette reservation : le prix negocie s'il existe
     * (fige au moment de l'acceptation de l'offre), sinon le prix public
     * courant de l'annonce. Le prix negocie n'affecte jamais Annonce::prix,
     * qui reste inchange pour les autres acheteurs.
     */
    public function prixEffectif(): float
    {
        return $this->prix_negocie !== null ? (float) $this->prix_negocie : (float) $this->annonce->prix;
    }
}
