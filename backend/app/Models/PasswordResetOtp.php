<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PasswordResetOtp extends Model
{
    protected $fillable = ['email', 'otp', 'expires_at'];

    protected $casts = ['expires_at' => 'datetime'];

    public static function generer(string $email): self
    {
        self::where('email', $email)->delete();

        return self::create([
            'email'      => $email,
            'otp'        => str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT),
            'expires_at' => now()->addMinutes(10),
        ]);
    }

    public function estValide(string $code): bool
    {
        return $this->otp === $code && now()->lessThanOrEqualTo($this->expires_at);
    }
}
