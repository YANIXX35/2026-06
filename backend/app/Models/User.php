<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens, HasRoles;

    protected $fillable = [
        'nom', 'prenom', 'email', 'telephone', 'password',
        'role', 'statut', 'photo', 'adresse', 'latitude', 'longitude',
        'type_structure', 'nom_structure', 'description_structure', 'note_moyenne',
        'type_acheteur', 'nom_organisation',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'note_moyenne' => 'decimal:2',
        ];
    }

    public function annonces()
    {
        return $this->hasMany(Annonce::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function avisRecus()
    {
        return $this->hasMany(Avis::class, 'fournisseur_id');
    }

    public function avisEmis()
    {
        return $this->hasMany(Avis::class);
    }

    public function signalements()
    {
        return $this->hasMany(Signalement::class);
    }

    public function conversations()
    {
        return Conversation::where('user_1_id', $this->id)
            ->orWhere('user_2_id', $this->id);
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function isFournisseur(): bool
    {
        return $this->role === 'fournisseur';
    }

    public function isAcheteur(): bool
    {
        return $this->role === 'acheteur';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
}
