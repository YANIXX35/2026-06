<?php

namespace App\Observers;

use App\Models\Annonce;
use Illuminate\Support\Facades\Cache;

class AnnonceObserver
{
    public function saved(Annonce $annonce): void
    {
        $this->clearWelcomeCache();
    }

    public function deleted(Annonce $annonce): void
    {
        $this->clearWelcomeCache();
    }

    private function clearWelcomeCache(): void
    {
        Cache::forget('welcome_stats');
        Cache::forget('welcome_annonces');
        Cache::forget('welcome_categories');
        Cache::forget('welcome_impact');
    }
}
