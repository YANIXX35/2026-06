<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Offre;
use App\Models\Reservation;
use App\Notifications\ContreOffre;
use App\Notifications\NouvelleOffre;
use App\Notifications\OffreAcceptee;
use App\Notifications\OffreRefusee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Négociation de prix structurée, tracée en base, entre un acheteur et le
 * fournisseur d'une annonce. Objectif : que toute proposition de prix reste
 * dans l'application (horodatée, non modifiable a posteriori) plutôt que
 * dans un message texte libre ou un échange hors plateforme dont la
 * plateforme ne pourrait pas témoigner en cas de litige.
 *
 * Cycle de négociation :
 *   1. Acheteur propose        → store()
 *   2. Fournisseur contre-propose → contreOffrir()
 *      OU accepte              → accepter()
 *      OU refuse               → refuser()
 *   3. Acheteur peut accepter/refuser la contre-offre → accepter()/refuser()
 *      (seul le destinataire peut accepter/refuser à chaque étape)
 */
class OffreController extends Controller
{
    // ─── ACHETEUR : propose un prix ─────────────────────────────────────────

    public function store(Request $request, Conversation $conversation)
    {
        if (!Schema::hasTable('offres')) {
            return back()->with('error', 'La négociation de prix n\'est pas encore disponible, réessayez dans quelques minutes.');
        }

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
                'annonce_id'       => $annonce->id,
                'conversation_id'  => $conversation->id,
                'acheteur_id'      => $userId,
                'fournisseur_id'   => $annonce->user_id,
                'proposeur_id'     => $userId,
                'prix_propose'     => $request->prix_propose,
                'quantite'         => $request->quantite,
                'message'          => $request->message,
                'statut'           => 'en_attente',
                'est_contre_offre' => false,
            ]);
        });

        $offre->load('annonce', 'acheteur');
        $annonce->user->notify(new NouvelleOffre($offre));

        return redirect()->route('messages.show', $conversation)
            ->with('success', 'Votre offre a été envoyée. Le fournisseur sera notifié.');
    }

    // ─── FOURNISSEUR : contre-propose un prix ───────────────────────────────

    /**
     * Le fournisseur peut répondre à une offre reçue par une contre-offre,
     * au lieu de simplement accepter ou refuser.
     * La contre-offre invalide l'offre précédente (statut → 'remplacee') et
     * crée une nouvelle offre avec est_contre_offre = true.
     * L'acheteur reçoit alors une notification et peut à son tour accepter,
     * refuser, ou re-proposer (via store()).
     */
    public function contreOffrir(Request $request, Offre $offrePrecedente)
    {
        $userId = Auth::id();

        // Seul le fournisseur de l'offre initiale peut contre-proposer
        if ($offrePrecedente->fournisseur_id !== $userId) abort(403);

        if ($offrePrecedente->statut !== 'en_attente') {
            return back()->with('error', 'Cette offre n\'est plus en attente, vous ne pouvez pas y répondre.');
        }

        $annonce = $offrePrecedente->annonce;
        if (!$annonce || $annonce->statut !== 'disponible') {
            return back()->with('error', 'Cette annonce n\'est plus disponible.');
        }

        $request->validate([
            'prix_propose' => 'required|numeric|min:1',
            'message'      => 'nullable|string|max:500',
        ]);

        $nouvelleOffre = null;

        DB::transaction(function () use ($request, $offrePrecedente, $annonce, $userId, &$nouvelleOffre) {
            // Invalider l'offre précédente
            $offrePrecedente->update(['statut' => 'remplacee']);

            // Créer la contre-offre (même quantité que l'offre initiale)
            $nouvelleOffre = Offre::create([
                'annonce_id'       => $annonce->id,
                'conversation_id'  => $offrePrecedente->conversation_id,
                'acheteur_id'      => $offrePrecedente->acheteur_id,
                'fournisseur_id'   => $userId,
                'proposeur_id'     => $userId,
                'prix_propose'     => $request->prix_propose,
                'quantite'         => $offrePrecedente->quantite,
                'message'          => $request->message,
                'statut'           => 'en_attente',
                'est_contre_offre' => true,
            ]);
        });

        $nouvelleOffre->load('annonce', 'fournisseur', 'acheteur');
        // Notifier l'acheteur de la contre-offre
        $nouvelleOffre->acheteur->notify(new ContreOffre($nouvelleOffre));

        return redirect()
            ->route('messages.show', $offrePrecedente->conversation_id)
            ->with('success', 'Votre contre-offre a été envoyée à l\'acheteur.');
    }

    // ─── FOURNISSEUR (ou acheteur sur contre-offre) : accepte ───────────────

    public function accepter(Offre $offre)
    {
        $userId = Auth::id();

        // Sur une offre initiale    → seul le fournisseur peut accepter.
        // Sur une contre-offre      → seul l'acheteur peut accepter.
        $peutAccepter = $offre->est_contre_offre
            ? $offre->acheteur_id === $userId
            : $offre->fournisseur_id === $userId;

        if (!$peutAccepter) abort(403);

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

        $offre->load('annonce', 'acheteur', 'fournisseur');

        // Notifier l'autre partie
        if ($offre->est_contre_offre) {
            // L'acheteur vient d'accepter la contre-offre du fournisseur
            $offre->fournisseur->notify(new OffreAcceptee($offre));
            return redirect()->route('paiement.show')
                ->with('success', 'Offre acceptée ! Vous pouvez maintenant finaliser le paiement.');
        }

        // Le fournisseur vient d'accepter l'offre initiale de l'acheteur
        $offre->acheteur->notify(new OffreAcceptee($offre));
        return redirect()->route('reservations.mes-reservations')
            ->with('success', 'Offre acceptée, réservation créée. L\'acheteur a été notifié.');
    }

    // ─── FOURNISSEUR (ou acheteur sur contre-offre) : refuse ────────────────

    public function refuser(Offre $offre)
    {
        $userId = Auth::id();

        $peutRefuser = $offre->est_contre_offre
            ? $offre->acheteur_id === $userId
            : $offre->fournisseur_id === $userId;

        if (!$peutRefuser) abort(403);

        $offre->update(['statut' => 'refusee']);

        $offre->load('annonce', 'acheteur', 'fournisseur');

        // Notifier l'autre partie
        if ($offre->est_contre_offre) {
            $offre->fournisseur->notify(new OffreRefusee($offre));
        } else {
            $offre->acheteur->notify(new OffreRefusee($offre));
        }

        return back()->with('success', 'Offre refusée. La conversation reste ouverte pour continuer à négocier.');
    }
}
