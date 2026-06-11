<?php

namespace App\Console\Commands;

use App\Models\Annonce;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ExpirerAnnonces extends Command
{
    protected $signature   = 'annonces:expirer';
    protected $description = 'Passe à "expiré" les annonces dont la date_expiration est dépassée';

    public function handle(): int
    {
        $count = Annonce::where('statut', 'disponible')
            ->whereNotNull('date_expiration')
            ->where('date_expiration', '<', now())
            ->update(['statut' => 'expiré']);

        Log::info("ExpirerAnnonces: {$count} annonce(s) expirée(s).");
        $this->info("{$count} annonce(s) marquée(s) comme expirée(s).");

        return self::SUCCESS;
    }
}
