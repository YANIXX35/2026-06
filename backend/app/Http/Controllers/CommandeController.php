<?php

namespace App\Http\Controllers;

use App\Models\Annonce;
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

        $userId    = Auth::id();
        $cartItems = CartItem::with('annonce')->where('user_id', $userId)->get();

        if ($cartItems->isEmpty()) {
            return back()->with('error', 'Votre panier est vide.');
        }

        $erreur = null;

        DB::transaction(function () use ($cartItems, $request, $userId, &$erreur) {
            // Verrouiller les annonces pour éviter la race condition
            // (deux acheteurs commandant le même article simultanément)
            $annonceIds = $cartItems->pluck('annonce_id');
            $annonces   = Annonce::with('user')
                ->whereIn('id', $annonceIds)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            foreach ($cartItems as $item) {
                $annonce = $annonces->get($item->annonce_id);
                if (!$annonce || $annonce->statut !== 'disponible' || $annonce->estExpire()) {
                    $erreur = '« ' . ($item->annonce->titre ?? 'Article') . ' » n\'est plus disponible. Veuillez le retirer du panier.';
                    return;
                }
            }

            $total = $cartItems->sum(fn($i) => $i->sousTotal());

            $commande = Commande::create([
                'user_id'           => $userId,
                'statut'            => 'en_attente',
                'montant_total'     => $total,
                'adresse_livraison' => $request->adresse_livraison,
                'message'           => $request->message,
            ]);

            $parFournisseur = $cartItems->groupBy(fn($i) => $annonces->get($i->annonce_id)?->user_id);

            foreach ($cartItems as $item) {
                $annonce = $annonces->get($item->annonce_id);
                CommandeItem::create([
                    'commande_id'    => $commande->id,
                    'annonce_id'     => $item->annonce_id,
                    'fournisseur_id' => $annonce->user_id,
                    'quantite'       => $item->quantite,
                    'prix_unitaire'  => $annonce->type_offre === 'don' ? 0 : $annonce->prix,
                    'statut'         => 'en_attente',
                ]);
            }

            foreach ($parFournisseur as $fournisseurId => $fournisseurItems) {
                $fournisseur = $annonces->get($fournisseurItems->first()->annonce_id)?->user;
                if ($fournisseur) {
                    $fournisseur->notify(new NouvelleCommande($commande, $fournisseurItems->map(fn($i) => $annonces->get($i->annonce_id))->filter()->values()->all()));
                }
            }

            CartItem::where('user_id', $userId)->delete();
        });

        if ($erreur) {
            return back()->with('error', $erreur);
        }

        return redirect()->route('commandes.index')->with('success', 'Commande passée avec succès ! Les fournisseurs ont été notifiés.');
    }

    public function annuler(Commande $commande)
    {
        if ($commande->user_id !== Auth::id()) abort(403);
        if (!in_array($commande->statut, ['en_attente'])) {
            return back()->with('error', 'Cette commande ne peut plus être annulée.');
        }

        DB::transaction(function () use ($commande) {
            $commande->update(['statut' => 'annulée']);
        });

        return back()->with('success', 'Commande annulée.');
    }

    // ── ESPACE FOURNISSEUR ────────────────────────────────────────

    public function indexFournisseur()
    {
        $items = CommandeItem::with(['commande.acheteur', 'annonce.photoPrincipale'])
            ->where('fournisseur_id', Auth::id())
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('commandes.fournisseur', compact('items'));
    }

    public function accepterItem(CommandeItem $item)
    {
        if ($item->fournisseur_id !== Auth::id()) abort(403);
        if ($item->statut !== 'en_attente') {
            return back()->with('error', 'Cet article a déjà été traité.');
        }

        DB::transaction(function () use ($item) {
            $item->update(['statut' => 'accepté']);
        });

        return back()->with('success', 'Article accepté.');
    }

    public function refuserItem(CommandeItem $item)
    {
        if ($item->fournisseur_id !== Auth::id()) abort(403);
        if ($item->statut !== 'en_attente') {
            return back()->with('error', 'Cet article a déjà été traité.');
        }

        DB::transaction(function () use ($item) {
            $item->update(['statut' => 'refusé']);
        });

        return back()->with('success', 'Article refusé.');
    }
}
