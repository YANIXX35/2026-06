<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = ['user_1_id', 'user_2_id'];

    public function user1()    { return $this->belongsTo(User::class, 'user_1_id'); }
    public function user2()    { return $this->belongsTo(User::class, 'user_2_id'); }
    public function messages() { return $this->hasMany(Message::class)->orderBy('created_at'); }

    public function dernierMessage()
    {
        return $this->hasOne(Message::class)->latestOfMany();
    }

    public function nonLus(int $userId): int
    {
        return $this->messages()->where('lu', false)->where('user_id', '!=', $userId)->count();
    }
}
