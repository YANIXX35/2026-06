<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Photo extends Model
{
    use HasFactory;

    protected $fillable = ['annonce_id', 'url', 'is_principale'];

    protected $casts = ['is_principale' => 'boolean'];

    public function annonce() { return $this->belongsTo(Annonce::class); }
}
