<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AbonnementCategorie extends Model
{
    protected $table = 'abonnements_categories';
    protected $fillable = ['user_id', 'categorie_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function categorie()
    {
        return $this->belongsTo(Categorie::class);
    }
}
