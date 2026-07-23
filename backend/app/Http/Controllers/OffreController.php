<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Offre;
use App\Models\Reservation;
use App\Notifications\NouvelleOffre;
use App\Notifications\OffreAcceptee;
use App\Notifications\OffreRefusee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Négociation de prix structurée, tracée en base, entre un acheteur et le
 * fournisseur d'une annonce. Objectif : que toute proposition de prix reste
 * dans l'application (horodatée, non modifiable a posteriori) plutôt que
 * dans un message texte libre ou un échange hors plateforme dont la
 * plateforme ne pourrait pas témoigner en cas de litige.
 *
 * Simplification assumée : seul l'acheteur propose un prix, seul le
 * fournisseur accepte/refuse (pas de contre-offre du fournisseur dans cette
 * première version).
 */
class OffreController extends Controller
{
    public function store(Request $request, Conversation $conversation)
    {
        $userId = Auth::id();
        if ($conversation->user_1_id !== $userId && $conversation->user_2_id !== $userId) abort(403);

        $annonce = $conversation->annonce;
        if (!$annonce) {
            return back()->with('error', 'Cette conversation n\'est liée à aucune annonce.');
        }
        if ($annonce->user_id === $userId) {
            return back()->with('error', 'Vous ne pouvez pas proposer un prix sur votre propre annonce.');
        }
        if ($annonce->type_offre !== 'vente') {
            return back()->with('error', 'La négociation de prix n\'est disponible que pour les annonces en vente.');
        }

        $request->validate([
            'prix_propose' => 'required|numeric|min:1',
            'quantite'     => 'required|numeric|min:0.01|max:' . $annonce->quantite,
            'message'      => 'nullable|string|max:500',
        ]);

        $offre = null;

        DB::transaction(function () use ($request, $conversation, $annonce, $userId, &$offre) {
            // Une nouvelle proposition remplace toute proposition encore en attente
            // sur cette conversation, pour ne jamais avoir deux offres actives
            // contradictoires en même temps.
            $conversation->offres()->enAttente()->update(['statut' => 'remplacee']);

            $offre = Offre::create([
                'annonce_id'      => $annonce->id,
                'conversation_id' => $conversation->id,
                'acheteur_id'     => $userId,
                'fournisseur_id'  => $annonce->user_id,
                'prix_propose'    => $request->prix_propose,
                'quantite'        => $request->quantite,
                'message'         => $request->message,
                'statut'          => 'en_attente',
            ]);
        });

        $offre->load('annonce', 'acheteur');
        $annonce->user->notify(new NouvelleOffre($offre));

        return redirect()->route('messages.show', $conversation)->with('success', 'Votre offre a été envoyée.');
    }

    public function accepter(Offre $offre)
    {
        if ($offre->fournisseur_id !== Auth::id()) abort(403);

        if (!$offre->peutEtreAcceptee()) {
            return back()->with('error', 'Cette offre ne peut plus être acceptée (annonce indisponible ou offre déjà traitée).');
        }

        $reservation = null;

        DB::transaction(function () use ($offre, &$reservation) {
            $reservation = Reservation::create([
                'annonce_id'        => $offre->annonce_id,
                'user_id'           => $offre->acheteur_id,
                'quantite_demandee' => $offre->quantite,
                'statut'            => 'en_attente',
                'prix_negocie'      => $offre->prix_propose,
                'offre_id'          => $offre->id,
            ]);

            if ($offre->annonce->statut === 'disponible') {
                $offre->annonce->update(['statut' => 'reservé']);
            }

            $offre->update(['statut' => 'acceptee']);
        });

        $offre->load('annonce', 'acheteur');
        $offre->acheteur->notify(new OffreAcceptee($offre));

        return redirect()->route('reservations.mes-reservations')->with('success', 'Offre acceptée, réservation créée.');
    }

    public function refuser(Offre $offre)
    {
        if ($offre->fournisseur_id !== Auth::id()) abort(403);

        $offre->update(['statut' => 'refusee']);

        $offre->load('annonce', 'acheteur');
        $offre->acheteur->notify(new OffreRefusee($offre));

        return back()->with('success', 'Offre refusée.');
    }
}
