<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class ResetUserPassword extends Command
{
    protected $signature   = 'user:reset-password {email} {password}';
    protected $description = 'Réinitialise le mot de passe d\'un utilisateur (usage: Shell Render uniquement)';

    public function handle(): int
    {
        $email    = $this->argument('email');
        $password = $this->argument('password');

        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("Aucun utilisateur trouvé avec l'email : {$email}");
            return self::FAILURE;
        }

        $user->update(['password' => Hash::make($password)]);

        $this->info("Mot de passe mis à jour pour : {$email} (rôle: {$user->role})");

        return self::SUCCESS;
    }
}
