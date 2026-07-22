<?php

namespace App\Providers;

use App\Models\Annonce;
use App\Models\Reservation;
use App\Observers\AnnonceObserver;
use App\Observers\ReservationObserver;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Paginator::useBootstrapFive();

        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }

        // Evite l'incohérence entre les compteurs/annonces de la page d'accueil (cachés
        // 5 min) et l'état réel : le cache est invalidé dès qu'une annonce ou une
        // réservation change de statut, plutôt que d'attendre l'expiration du TTL.
        Annonce::observe(AnnonceObserver::class);
        Reservation::observe(ReservationObserver::class);
    }
}
