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

        // Indicateurs d'impact calcules a partir des reservations reellement completees
        // (seule transition de statut qui signifie un echange effectivement realise).
        $impact = Cache::remember('welcome_impact', 300, function () {
            try {
                $completees = Reservation::with('annonce')->where('statut', 'complétée')->get();

                $kgSauves = $completees
                    ->filter(fn($r) => $r->annonce && $r->annonce->unite === 'kg')
                    ->sum('quantite_demandee');

                $fcfaEconomises = $completees
                    ->filter(fn($r) => $r->annonce && $r->annonce->type_offre !== 'don')
                    ->sum(fn($r) => $r->quantite_demandee * $r->annonce->prix);

                return [
                    'kgSauves'         => (float) $kgSauves,
                    // ~0,4 kg par repas et ~2,5 kg CO2e evites par kg de nourriture non gaspillee :
                    // ordres de grandeur couramment utilises pour ce type de communication (ADEME),
                    // a affiner si des donnees plus precises sont disponibles.
                    'repasEquivalents' => (int) round($kgSauves / 0.4),
                    'co2EviteTonnes'    => round($kgSauves * 2.5 / 1000, 1),
                    'fcfaEconomises'    => (float) $fcfaEconomises,
                ];
            } catch (\Exception $e) {
                return ['kgSauves' => 0, 'repasEquivalents' => 0, 'co2EviteTonnes' => 0, 'fcfaEconomises' => 0];
            }
        });

        return view('welcome', array_merge($stats, $impact, compact('shopAnnonces', 'allCats')));
    }
}
