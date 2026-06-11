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

    // Retourne toujours une URL complète, qu'il s'agisse d'un chemin local ou d'une URL Cloudinary
    protected function url(): Attribute
    {
        return Attribute::get(function ($value) {
            if (!$value) return null;
            if (str_starts_with($value, 'http')) return $value;
            return asset('storage/' . $value);
        });
    }
}
