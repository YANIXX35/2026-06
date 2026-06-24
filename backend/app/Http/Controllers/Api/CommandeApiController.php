<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Annonce;
use App\Models\CartItem;
use App\Models\Commande;
use App\Models\CommandeItem;
use App\Notifications\NouvelleCommande;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CommandeApiController extends Controller
{
    public function index()
    {
        $commandes = Commande::with(['items.annonce.photoPrincipale'])
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(15);

        return response()->json($commandes);
    }

    public function show(Commande $commande)
    {
        if ($commande->user_id !== Auth::id()) {
            return response()->json(['message' => 'Accès refusé.'], 403);
        }

        $commande->load(['items.annonce.photoPrincipale', 'items.fournisseur']);
        return response()->json($commande);
    }

    public function passer(Request $request)
    {
        $request->validate([
            'mode_paiement'    => 'required|in:wave,moov_money',
            'telephone'        => ['required', 'string', 'regex:/^\+\d{3}\d{10}$/'],
            'adresse_livraison'=> 'nullable|string|max:255',
            'message'          => 'nullable|string|max:500',
        ]);

        $userId    = Auth::id();
        $cartItems = CartItem::with('annonce')->where('user_id', $userId)->get();

        if ($cartItems->isEmpty()) {
            return response()->json(['message' => 'Votre panier est vide.'], 422);
        }

        $erreur   = null;
        $commande = null;

        DB::transaction(function () use ($cartItems, $request, $userId, &$erreur, &$commande) {
            $annonceIds = $cartItems->pluck('annonce_id');
            $annonces   = Annonce::with('user')
                ->whereIn('id', $annonceIds)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            foreach ($cartItems as $item) {
                $annonce = $annonces->get($item->annonce_id);
                if (!$annonce || $annonce->statut !== 'disponible' || $annonce->estExpire()) {
                    $erreur = '« ' . ($item->annonce->titre ?? 'Article') . ' » n\'est plus disponible.';
                    return;
                }
            }

            $total     = $cartItems->sum(fn($i) => $i->sousTotal());
            $reference = 'AGC-' . strtoupper(Str::random(8));

            $commande = Commande::create([
                'user_id'             => $userId,
                'statut'              => 'confirmée',
                'montant_total'       => $total,
                'adresse_livraison'   => $request->adresse_livraison,
                'message'             => $request->message,
                'mode_paiement'       => $request->mode_paiement,
                'telephone_paiement'  => $request->telephone,
                'reference_paiement'  => $reference,
                'statut_paiement'     => $total > 0 ? 'payé' : 'gratuit',
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
                    $fournisseur->notify(new NouvelleCommande(
                        $commande,
                        $fournisseurItems->map(fn($i) => $annonces->get($i->annonce_id))->filter()->values()->all()
                    ));
                }
            }

            CartItem::where('user_id', $userId)->delete();
        });

        if ($erreur) {
            return response()->json(['message' => $erreur], 422);
        }

        return response()->json([
            'message'  => 'Commande confirmée !',
            'commande' => $commande,
        ], 201);
    }
}
