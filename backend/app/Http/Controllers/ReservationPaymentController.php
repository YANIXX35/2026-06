<?php

namespace App\Http\Controllers;

use App\Models\Commande;
use App\Models\CommandeItem;
use App\Models\Reservation;
use App\Notifications\NouvelleCommande;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Parcours de paiement dédié aux réservations issues d'une négociation.
 *
 * Pourquoi ce controller séparé ?
 * ─────────────────────────────────────────────────────────────────
 * PaymentController (existant) calcule le montant à payer à partir de
 * CartItem::sousTotal(), qui utilise toujours Annonce::prix (le prix public).
 *
 * Quand une offre de prix a été négociée et acceptée, la Reservation contient
 * un champ prix_negocie (ex: 300 FCFA alors que l'annonce affiche 500 FCFA).
 * Ce prix ne doit s'appliquer QU'à cet acheteur, sans toucher l'annonce elle-même.
 *
 * Ce controller expose un parcours parallèle :
 *   1. show($reservation)   → page de paiement affichant le prix négocié
 *   2. confirmer($reservation) → crée la Commande au prix négocié
 *
 * L'annonce reste à 500 FCFA pour tous les autres utilisateurs.
 */
class ReservationPaymentController extends Controller
{
    /**
     * Afficher la page de paiement pour une réservation issue d'une négociation.
     * Accessible uniquement par l'acheteur concerné.
     */
    public function show(Reservation $reservation)
    {
        $this->autoriser($reservation);

        $reservation->load(['annonce.photoPrincipale', 'annonce.categorie', 'annonce.user', 'offre']);

        if (!$reservation->prix_negocie) {
            // Pas de prix négocié → renvoyer vers le parcours panier classique
            return redirect()->route('paiement.show')
                ->with('info', 'Cette réservation n\'a pas de prix négocié. Utilisez le panier pour payer.');
        }

        if ($reservation->statut === 'complétée') {
            return redirect()->route('reservations.mes-reservations')
                ->with('info', 'Cette réservation a déjà été complétée.');
        }

        return view('paiement.reservation', compact('reservation'));
    }

    /**
     * Confirmer le paiement d'une réservation négociée.
     * Crée une Commande au prix_negocie, pas au prix public de l'annonce.
     */
    public function confirmer(Request $request, Reservation $reservation)
    {
        $this->autoriser($reservation);

        $request->validate([
            'mode_paiement' => 'required|in:wave,moov_money',
            'telephone'     => ['required', 'string', 'regex:/^\+\d{3}\d{10}$/'],
        ]);

        $reservation->load(['annonce.user', 'offre']);

        if (!$reservation->prix_negocie) {
            return redirect()->route('paiement.show')
                ->with('error', 'Prix négocié introuvable. Utilisez le parcours panier.');
        }

        if (!$reservation->annonce || $reservation->annonce->statut === 'expiré') {
            return back()->with('error', 'L\'annonce associée n\'est plus disponible.');
        }

        $commande = null;

        DB::transaction(function () use ($request, $reservation, &$commande) {
            // Prix utilisé = prix_negocie de la réservation (ex: 300 FCFA)
            // L'annonce reste à 500 FCFA pour tout le monde.
            $prixUnitaire = (float) $reservation->prix_negocie;
            $total        = $prixUnitaire * (float) $reservation->quantite_demandee;
            $reference    = 'AGC-NEG-' . strtoupper(Str::random(6));

            // Calcul de la commission de 5%
            $commissionTaux = 0.05;
            $montantCommission = $total * $commissionTaux;
            $montantNetFournisseur = $total - $montantCommission;

            $commande = Commande::create([
                'user_id'                 => $reservation->user_id,
                'statut'                  => 'confirmée',
                'montant_total'           => $total,
                'commission_taux'         => $commissionTaux,
                'montant_commission'      => $montantCommission,
                'montant_net_fournisseur' => $montantNetFournisseur,
                'adresse_livraison'       => null,
                'message'                 => 'Commande issue d\'une négociation de prix. Offre acceptée.',
                'mode_paiement'           => $request->mode_paiement,
                'telephone_paiement'      => $request->telephone,
                'reference_paiement'      => $reference,
                'statut_paiement'         => $total > 0 ? 'payé' : 'gratuit',
            ]);

            // CommandeItem au prix négocié
            CommandeItem::create([
                'commande_id'        => $commande->id,
                'annonce_id'         => $reservation->annonce_id,
                'fournisseur_id'     => $reservation->annonce->user_id,
                'quantite'           => $reservation->quantite_demandee,
                'prix_unitaire'      => $prixUnitaire,
                'commission_montant' => $montantCommission,
                'montant_net'        => $montantNetFournisseur,
                'statut'             => 'en_attente',
            ]);

            // Déduction du stock de l'annonce suite à la négociation payée
            $annonce = $reservation->annonce;
            $nouvelleQuantite = max(0, (float) $annonce->quantite - (float) $reservation->quantite_demandee);
            $nouveauStatut = $nouvelleQuantite <= 0 ? 'reservé' : 'disponible';
            $annonce->update([
                'quantite' => $nouvelleQuantite,
                'statut'   => $nouveauStatut,
            ]);

            // Marquer la réservation comme acceptée/payée
            $reservation->update(['statut' => 'acceptée']);

            // Notifier le fournisseur
            $reservation->annonce->user->notify(
                new NouvelleCommande($commande, [$reservation->annonce])
            );
        });

        return redirect()->route('paiement.reservation.succes', $reservation)
            ->with('success', 'Paiement confirmé au prix négocié !');
    }

    /**
     * Page de succès après paiement d'une réservation négociée.
     */
    public function succes(Reservation $reservation)
    {
        $this->autoriser($reservation);
        $reservation->load(['annonce.photoPrincipale', 'annonce.user']);
        return view('paiement.reservation-succes', compact('reservation'));
    }

    /**
     * Vérifier que l'utilisateur connecté est bien l'acheteur de cette réservation.
     */
    private function autoriser(Reservation $reservation): void
    {
        if ($reservation->user_id !== Auth::id()) {
            abort(403, 'Vous n\'êtes pas autorisé à accéder à ce paiement.');
        }
    }
}
