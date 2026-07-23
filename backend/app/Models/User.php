<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cache;
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
        'social_provider', 'social_id',
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

    public function conversations(): \Illuminate\Database\Eloquent\Builder
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

    /**
     * Calcule l'impact écologique et économique global de l'utilisateur
     * Résultat mis en cache 10 minutes pour éviter des requêtes DB répétées.
     */
    public function getImpactMetricsAttribute(): array
    {
        return Cache::remember('user_impact_' . $this->id, 600, function () {
            // 1. Commandes complétées / payées
            $commandesPayees = Commande::where('user_id', $this->id)
                ->whereIn('statut', ['payee', 'livree', 'terminee', 'confirmee'])
                ->with('items.annonce')
                ->get();

            $kgSauves = 0.0;
            $economiesFcfa = 0.0;
            $nbCommandes = $commandesPayees->count();

            foreach ($commandesPayees as $cmd) {
                foreach ($cmd->items as $item) {
                    $annonce = $item->annonce;
                    $qte = (float) $item->quantite;
                    if ($annonce) {
                        $poidsUnit = (float) ($annonce->poids_estime_kg > 0 ? $annonce->poids_estime_kg : 1.0);
                        $kgSauves += ($qte * $poidsUnit);
                        $economiesFcfa += ($annonce->economieEstimee() * $qte);
                    } else {
                        $kgSauves += $qte;
                    }
                }
            }

            // 2. Réservations acceptées ou complétées
            $reservations = Reservation::where('user_id', $this->id)
                ->whereIn('statut', ['acceptée', 'complétée', 'terminee'])
                ->with('annonce')
                ->get();

            foreach ($reservations as $res) {
                $annonce = $res->annonce;
                $qte = (float) $res->quantite;
                if ($annonce) {
                    $poidsUnit = (float) ($annonce->poids_estime_kg > 0 ? $annonce->poids_estime_kg : 1.0);
                    $kgSauves += ($qte * $poidsUnit);
                    $economiesFcfa += ($annonce->economieEstimee() * $qte);
                } else {
                    $kgSauves += $qte;
                }
            }

            if ($kgSauves == 0 && ($this->annonces()->count() > 0 || $nbCommandes > 0)) {
                $kgSauves = (float) ($this->annonces()->count() * 2.5);
                $economiesFcfa = (float) ($this->annonces()->count() * 1500);
            }

            $co2EviteKg = round($kgSauves * 2.5, 1);

            return [
                'kg_sauves'      => round($kgSauves, 1),
                'co2_evite_kg'   => $co2EviteKg,
                'economies_fcfa' => round($economiesFcfa, 0),
                'total_actions'  => $nbCommandes + $reservations->count(),
            ];
        });
    }

    /**
     * Retourne la liste des badges débloqués par l'utilisateur
     */
    public function getBadgesAttribute(): array
    {
        $metrics = $this->impact_metrics;
        $kg = $metrics['kg_sauves'];
        $actions = $metrics['total_actions'];

        $badges = [
            [
                'id'          => 'premier_pas',
                'nom'         => 'Premier Pas',
                'description' => 'A réalisé au moins 1 action anti-gaspillage sur la plateforme',
                'icone'       => '🌱',
                'debloque'    => $actions >= 1 || $kg >= 1,
                'progres'     => min(100, round(($actions / 1) * 100)),
            ],
            [
                'id'          => 'eco_guerrier',
                'nom'         => 'Éco-Guerrier',
                'description' => 'A sauvé au moins 5 kg de nourriture du gaspillage',
                'icone'       => '🍃',
                'debloque'    => $kg >= 5,
                'progres'     => min(100, round(($kg / 5) * 100)),
            ],
            [
                'id'          => 'heros_antigaspi',
                'nom'         => 'Héros Anti-Gaspi',
                'description' => 'A évité l\'émission de plus de 25 kg de CO₂',
                'icone'       => '🌍',
                'debloque'    => $metrics['co2_evite_kg'] >= 25,
                'progres'     => min(100, round(($metrics['co2_evite_kg'] / 25) * 100)),
            ],
            [
                'id'          => 'legende_verte',
                'nom'         => 'Légende Verte',
                'description' => 'A économisé plus de 20 000 FCFA tout en préservant la planète',
                'icone'       => '👑',
                'debloque'    => $metrics['economies_fcfa'] >= 20000,
                'progres'     => min(100, round(($metrics['economies_fcfa'] / 20000) * 100)),
            ],
        ];

        return $badges;
    }
}
