<?php

namespace App\Http\Controllers;

use App\Models\Annonce;
use App\Models\Categorie;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class WelcomeController extends Controller
{
    public function __invoke()
    {
        // Cache 5 minutes — evite 6 requetes DB a chaque visite
        $stats = Cache::remember('welcome_stats', 300, function () {
            try {
                return [
                    'nbAnnonces'     => Annonce::where('statut', 'disponible')->count(),
                    'nbFournisseurs' => User::where('role', 'fournisseur')->count(),
                    'nbEchanges'     => Reservation::where('statut', 'complétée')->count(),
                    'nbCategories'   => Categorie::count(),
                ];
            } catch (\Exception $e) {
                return ['nbAnnonces' => 0, 'nbFournisseurs' => 0, 'nbEchanges' => 0, 'nbCategories' => 0];
            }
        });

        $shopAnnonces = Cache::remember('welcome_annonces', 300, function () {
            try {
                return Annonce::with(['categorie', 'photoPrincipale', 'user'])
                    ->where('statut', 'disponible')
                    ->latest()
                    ->limit(8)
                    ->get();
            } catch (\Exception $e) {
                return collect();
            }
        });

        $allCats = Cache::remember('welcome_categories', 300, function () {
            try {
                return Categorie::withCount(['annonces' => fn($q) => $q->where('statut', 'disponible')])->get();
            } catch (\Exception $e) {
                return collect();
            }
        });

        return view('welcome', array_merge($stats, compact('shopAnnonces', 'allCats')));
    }
}
