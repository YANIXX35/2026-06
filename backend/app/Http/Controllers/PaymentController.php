<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Commande;
use App\Models\CommandeItem;
use App\Models\Annonce;
use App\Notifications\NouvelleCommande;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    public function initier(Request $request)
    {
        $request->validate([
            'adresse_livraison' => 'nullable|string|max:255',
            'message'           => 'nullable|string|max:500',
        ]);

        $items = CartItem::with('annonce')->where('user_id', Auth::id())->get();
        if ($items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Votre panier est vide.');
        }

        session([
            'checkout.adresse' => $request->adresse_livraison,
            'checkout.message' => $request->message,
        ]);

        return redirect()->route('paiement.show');
    }

    public function show()
    {
        $items = CartItem::with(['annonce.photoPrincipale', 'annonce.categorie', 'annonce.user'])
            ->where('user_id', Auth::id())
            ->get();

        if ($items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Votre panier est vide.');
        }

        $total   = $items->sum(fn($i) => $i->sousTotal());
        $adresse = session('checkout.adresse');
        $message = session('checkout.message');

        return view('paiement.index', compact('items', 'total', 'adresse', 'message'));
    }

    public function confirmer(Request $request)
    {
        $request->validate([
            'mode_paiement' => 'required|in:wave,moov_money',
            'telephone'     => ['required', 'string', 'regex:/^\+\d{3}\d{10}$/'],
        ]);

        $userId    = Auth::id();
        $cartItems = CartItem::with('annonce')->where('user_id', $userId)->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Votre panier est vide.');
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
            
            // Calcul de la commission de 5%
            $commissionTaux = 0.05;
            $montantCommission = $total * $commissionTaux;
            $montantNetFournisseur = $total - $montantCommission;

            $commande = Commande::create([
                'user_id'                 => $userId,
                'statut'                  => 'confirmée',
                'montant_total'           => $total,
                'commission_taux'         => $commissionTaux,
                'montant_commission'      => $montantCommission,
                'montant_net_fournisseur' => $montantNetFournisseur,
                'adresse_livraison'       => session('checkout.adresse'),
                'message'                 => session('checkout.message'),
                'mode_paiement'           => $request->mode_paiement,
                'telephone_paiement'      => $request->telephone,
                'reference_paiement'      => $reference,
                'statut_paiement'         => $total > 0 ? 'payé' : 'gratuit',
            ]);

            $parFournisseur = $cartItems->groupBy(fn($i) => $annonces->get($i->annonce_id)?->user_id);

            foreach ($cartItems as $item) {
                $annonce = $annonces->get($item->annonce_id);
                $prixUnitaire = $item->prixUnitaire();
                $sousTotalItem = $item->sousTotal();
                $commissionItem = $sousTotalItem * $commissionTaux;
                $netItem = $sousTotalItem - $commissionItem;

                CommandeItem::create([
                    'commande_id'        => $commande->id,
                    'annonce_id'         => $item->annonce_id,
                    'fournisseur_id'     => $annonce->user_id,
                    'quantite'           => $item->quantite,
                    'prix_unitaire'      => $prixUnitaire,
                    'commission_montant' => $commissionItem,
                    'montant_net'        => $netItem,
                    'statut'             => 'en_attente',
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
            session()->forget(['checkout.adresse', 'checkout.message']);
        });

        if ($erreur) {
            return redirect()->route('paiement.show')->with('error', $erreur);
        }

        return redirect()->route('paiement.succes', ['commande' => $commande->id]);
    }

    public function succes(Commande $commande)
    {
        if ($commande->user_id !== Auth::id()) abort(403);
        $commande->load(['items.annonce.photoPrincipale', 'items.fournisseur']);
        return view('paiement.succes', compact('commande'));
    }
}
