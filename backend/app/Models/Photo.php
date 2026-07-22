<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Photo extends Model
{
    use HasFactory;

    protected $fillable = ['annonce_id', 'url', 'is_principale'];

    protected $casts = ['is_principale' => 'boolean'];

    public function annonce() { return $this->belongsTo(Annonce::class); }

    // Retourne toujours une URL complète cloud ou valide, évitant les 404 lors des redémarrages de conteneur
    protected function url(): Attribute
    {
        return Attribute::get(function ($value) {
            if (!$value) return null;
            if (str_starts_with($value, 'http')) return $value;

            // Sur un serveur comme Render à stockage éphémère, si le fichier local n'existe plus :
            if (file_exists(public_path('storage/' . $value))) {
                return asset('storage/' . $value);
            }

            return null;
        });
    }
}
