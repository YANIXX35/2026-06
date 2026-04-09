<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Avis extends Model
{
    use HasFactory;

    protected $fillable = ['reservation_id', 'user_id', 'fournisseur_id', 'note', 'commentaire'];

    public function reservation() { return $this->belongsTo(Reservation::class); }
    public function auteur()      { return $this->belongsTo(User::class, 'user_id'); }
    public function fournisseur() { return $this->belongsTo(User::class, 'fournisseur_id'); }
}
