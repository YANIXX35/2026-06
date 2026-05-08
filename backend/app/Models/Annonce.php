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
}
