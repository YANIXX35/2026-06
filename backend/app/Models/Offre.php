<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Offre extends Model
{
    protected $fillable = [
        'annonce_id',
        'conversation_id',
        'acheteur_id',
        'fournisseur_id',
        'proposeur_id',
        'prix_propose',
        'quantite',
        'statut',
        'message',
        'est_contre_offre',
    ];

    protected $casts = [
        'prix_propose'    => 'decimal:2',
        'quantite'        => 'decimal:2',
        'est_contre_offre'=> 'boolean',
    ];

    public function annonce()
    {
        return $this->belongsTo(Annonce::class);
    }

    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    public function acheteur()
    {
        return $this->belongsTo(User::class, 'acheteur_id');
    }

    public function fournisseur()
    {
        return $this->belongsTo(User::class, 'fournisseur_id');
    }

    /**
     * L'utilisateur qui a réellement émis cette proposition de prix.
     * Pour une offre initiale : l'acheteur.
     * Pour une contre-offre du fournisseur : le fournisseur.
     * Utilise proposeur_id s'il est renseigné, sinon repli sur acheteur_id
     * pour la rétro-compatibilité avec les offres antérieures à cette migration.
     */
    public function proposeur()
    {
        return $this->belongsTo(User::class, 'proposeur_id');
    }

    public function scopeEnAttente($query)
    {
        return $query->where('statut', 'en_attente');
    }

    /**
     * Retourne l'utilisateur qui a proposé cette offre, avec repli
     * sur acheteur pour la rétro-compatibilité.
     */
    public function getProposeurEffectifAttribute(): ?User
    {
        if ($this->proposeur_id && $this->proposeur) {
            return $this->proposeur;
        }
        return $this->acheteur;
    }

    /**
     * Une offre ne peut etre acceptee que si l'annonce est toujours disponible
     * et de type "vente" (negociation non pertinente pour un don ou une
     * annonce a prix impose) - meme garde-fou que celui deja utilise dans
     * PaymentController pour eviter la double vente d'une meme annonce.
     */
    public function peutEtreAcceptee(): bool
    {
        return $this->statut === 'en_attente'
            && $this->annonce->statut === 'disponible'
            && $this->annonce->type_offre === 'vente';
    }
}
