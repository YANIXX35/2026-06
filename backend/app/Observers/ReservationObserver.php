<?php

namespace App\Observers;

use App\Models\Reservation;
use Illuminate\Support\Facades\Cache;

class ReservationObserver
{
    public function saved(Reservation $reservation): void
    {
        Cache::forget('welcome_stats');
        Cache::forget('welcome_impact');
    }

    public function deleted(Reservation $reservation): void
    {
        Cache::forget('welcome_stats');
        Cache::forget('welcome_impact');
    }
}
