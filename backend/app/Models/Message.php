<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Message extends Model
{
    use HasFactory;

    protected $fillable = ['conversation_id', 'user_id', 'contenu', 'lu'];

    protected $casts = ['lu' => 'boolean'];

    public function conversation() { return $this->belongsTo(Conversation::class); }
    public function auteur()       { return $this->belongsTo(User::class, 'user_id'); }
}
