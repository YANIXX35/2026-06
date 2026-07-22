<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Annonce extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'categorie_id', 'titre', 'description',
        'quantite', 'unite', 'prix', 'type_offre', 'statut',
        'date_expiration', 'latitude', 'longitude', 'adresse_collecte', 'vues',
    ];

    protected function casts(): array
    {
        return [
            'date_expiration' => 'datetime',
            'prix' => 'decimal:2',
            'quantite' => 'decimal:2',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function categorie()
    {
        return $this->belongsTo(Categorie::class);
    }

    public function photos()
    {
        return $this->hasMany(Photo::class);
    }

    public function photoPrincipale()
    {
        return $this->hasOne(Photo::class)->where('is_principale', true);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function signalements()
    {
        return $this->hasMany(Signalement::class);
    }

    public function estExpire(): bool
    {
        return $this->date_expiration && $this->date_expiration->isPast();
    }

    public function estUrgent(): bool
    {
        return $this->date_expiration
            && !$this->date_expiration->isPast()
            && $this->date_expiration->diffInHours(now()) <= 24;
    }

    public function heuresRestantes(): int
    {
        if (!$this->date_expiration || $this->date_expiration->isPast()) return 0;
        return (int) now()->diffInHours($this->date_expiration);
    }

    public function scopeDisponible($query)
    {
        return $query->where('statut', 'disponible');
    }

    public function scopeUrgent($query)
    {
        return $query->where('statut', 'disponible')
            ->whereNotNull('date_expiration')
            ->where('date_expiration', '>', now())
            ->where('date_expiration', '<=', now()->addHours(24));
    }

    public function incrementerVues(): void
    {
        $this->increment('vues');
    }

    public function getImageUrlAttribute(): string
    {
        if ($this->photoPrincipale && !empty($this->photoPrincipale->url)) {
            return $this->photoPrincipale->url;
        }

        if ($this->relationLoaded('photos') && $this->photos && $this->photos->count() > 0 && !empty($this->photos->first()->url)) {
            return $this->photos->first()->url;
        }

        return $this->default_image_by_category;
    }

    public function getDefaultImageByCategoryAttribute(): string
    {
        $catNom = mb_strtolower($this->categorie?->nom ?? '');

        if (str_contains($catNom, 'lait') || str_contains($catNom, 'fromage') || str_contains($catNom, 'yaourt')) {
            return 'https://images.unsplash.com/photo-1550583724-b2692b85b150?auto=format&fit=crop&w=800&q=80';
        }
        if (str_contains($catNom, 'fruit') || str_contains($catNom, 'légume') || str_contains($catNom, 'legume')) {
            return 'https://images.unsplash.com/photo-1610832958506-aa56368176cf?auto=format&fit=crop&w=800&q=80';
        }
        if (str_contains($catNom, 'boulangerie') || str_contains($catNom, 'pain') || str_contains($catNom, 'pâtisserie') || str_contains($catNom, 'patisserie')) {
            return 'https://images.unsplash.com/photo-1509440159596-0249088772ff?auto=format&fit=crop&w=800&q=80';
        }
        if (str_contains($catNom, 'viande') || str_contains($catNom, 'poisson') || str_contains($catNom, 'boucherie')) {
            return 'https://images.unsplash.com/photo-1607623814075-e51df1bdc82f?auto=format&fit=crop&w=800&q=80';
        }
        if (str_contains($catNom, 'boisson') || str_contains($catNom, 'jus')) {
            return 'https://images.unsplash.com/photo-1513558161293-cdaf765ed2fd?auto=format&fit=crop&w=800&q=80';
        }
        if (str_contains($catNom, 'épicerie') || str_contains($catNom, 'epicerie') || str_contains($catNom, 'riz') || str_contains($catNom, 'céréale') || str_contains($catNom, 'cereale')) {
            return 'https://images.unsplash.com/photo-1586201375761-83865001e31c?auto=format&fit=crop&w=800&q=80';
        }
        if (str_contains($catNom, 'animal') || str_contains($catNom, 'élevage') || str_contains($catNom, 'elevage')) {
            return 'https://images.unsplash.com/photo-1500595046743-cd271d694d30?auto=format&fit=crop&w=800&q=80';
        }

        // Image culinaire / nourriture générale de très haute qualité
        return 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=800&q=80';
    }
}
