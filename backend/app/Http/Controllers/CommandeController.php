<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Commande;
use App\Models\CommandeItem;
use App\Notifications\NouvelleCommande;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CommandeController extends Controller
{
    public function index()
    {
        $commandes = Commande::with(['items.annonce', 'items.fournisseur'])
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('commandes.index', compact('commandes'));
    }

    public function show(Commande $commande)
    {
        if ($commande->user_id !== Auth::id()) abort(403);

        $commande->load(['items.annonce.photoPrincipale', 'items.fournisseur']);

        return view('commandes.show', compact('commande'));
    }

    public function passer(Request $request)
    {
        $request->validate([
            'adresse_livraison' => 'nullable|string|max:255',
            'message'           => 'nullable|string|max:500',
        ]);

        $items = CartItem::with(['annonce.user'])
            ->where('user_id', Auth::id())
            ->get();

        if ($items->isEmpty()) {
            return back()->with('error', 'Votre panier est vide.');
        }

        // Vérifier que toutes les annonces sont encore disponibles
        foreach ($items as $item) {
            if (!$item->annonce || $item->annonce->statut !== 'disponible' || $item->annonce->estExpire()) {
                return back()->with('error', '« ' . ($item->annonce->titre ?? 'Article') . ' » n\'est plus disponible. Veuillez le retirer du panier.');
            }
        }

        DB::transaction(function () use ($items, $request) {
            $total = $items->sum(fn($i) => $i->sousTotal());

            $commande = Commande::create([
                'user_id'           => Auth::id(),
                'statut'            => 'en_attente',
                'montant_total'     => $total,
                'adresse_livraison' => $request->adresse_livraison,
                'message'           => $request->message,
            ]);

            // Grouper les items par fournisseur pour les notifier
            $parFournisseur = $items->groupBy(fn($i) => $i->annonce->user_id);

            foreach ($items as $item) {
                CommandeItem::create([
                    'commande_id'   => $commande->id,
                    'annonce_id'    => $item->annonce_id,
                    'fournisseur_id'=> $item->annonce->user_id,
                    'quantite'      => $item->quantite,
                    'prix_unitaire' => $item->annonce->type_offre === 'don' ? 0 : $item->annonce->prix,
                    'statut'        => 'en_attente',
                ]);
            }

            // Notifier chaque fournisseur concerné
            foreach ($parFournisseur as $fournisseurId => $fournisseurItems) {
                $fournisseur = $fournisseurItems->first()->annonce->user;
                if ($fournisseur) {
                    $fournisseur->notify(new NouvelleCommande($commande, $fournisseurItems->pluck('annonce')->all()));
                }
            }

            // Vider le panier
            CartItem::where('user_id', Auth::id())->delete();
        });

        return redirect()->route('commandes.index')->with('success', 'Commande passée avec succès ! Les fournisseurs ont été notifiés.');
    }

    public function annuler(Commande $commande)
    {
        if ($commande->user_id !== Auth::id()) abort(403);
        if (!in_array($commande->statut, ['en_attente'])) {
            return back()->with('error', 'Cette commande ne peut plus être annulée.');
        }
        $commande->update(['statut' => 'annulée']);
        return back()->with('success', 'Commande annulée.');
    }
}
