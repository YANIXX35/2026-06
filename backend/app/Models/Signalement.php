<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Signalement extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'annonce_id', 'signale_user_id', 'raison', 'description', 'statut'];

    public function auteur()        { return $this->belongsTo(User::class, 'user_id'); }
    public function annonce()       { return $this->belongsTo(Annonce::class); }
    public function userSignale()   { return $this->belongsTo(User::class, 'signale_user_id'); }
}
