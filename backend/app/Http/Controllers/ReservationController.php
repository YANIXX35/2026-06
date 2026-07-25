<?php

namespace App\Http\Controllers;

use App\Models\Annonce;
use App\Models\Reservation;
use App\Mail\NouvelleReservationMail;
use App\Mail\ReservationAccepteeMail;
use App\Mail\ReservationRefuseeMail;
use App\Notifications\ReservationAcceptee;
use App\Notifications\ReservationRefusee;
use App\Notifications\ReservationCompletee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class ReservationController extends Controller
{
    public function store(Request $request, Annonce $annonce)
    {
        $request->validate([
            'quantite_demandee'       => 'required|numeric|min:0.01',
            'message'                 => 'nullable|string|max:500',
            'date_collecte_souhaitee' => 'nullable|date|after:now',
        ]);

        $reservation = null;

        DB::transaction(function () use ($request, $annonce, &$reservation) {
            $reservation = Reservation::create([
                'annonce_id'              => $annonce->id,
                'user_id'                 => Auth::id(),
                'quantite_demandee'       => $request->quantite_demandee,
                'message'                 => $request->message,
                'date_collecte_souhaitee' => $request->date_collecte_souhaitee,
                'statut'                  => 'en_attente',
            ]);

            if ($annonce->statut === 'disponible') {
                $annonce->update(['statut' => 'reservé']);
            }
        });

        // Email envoyé hors transaction (appel réseau, non rollbackable)
        $reservation->load(['annonce.user', 'acheteur']);
        Mail::to($annonce->user->email)->send(new NouvelleReservationMail($reservation));

        return redirect()->route('annonces.show', $annonce)->with('success', 'Réservation envoyée !');
    }

    public function accepter(Reservation $reservation)
    {
        if ($reservation->annonce->user_id !== Auth::id()) abort(403);

        DB::transaction(function () use ($reservation) {
            $reservation->update(['statut' => 'acceptée']);
        });

        $reservation->load(['annonce', 'acheteur']);
        $reservation->acheteur->notify(new ReservationAcceptee($reservation));
        Mail::to($reservation->acheteur->email)->send(new ReservationAccepteeMail($reservation));

        return back()->with('success', 'Réservation acceptée.');
    }

    public function refuser(Reservation $reservation)
    {
        if ($reservation->annonce->user_id !== Auth::id()) abort(403);

        DB::transaction(function () use ($reservation) {
            $reservation->update(['statut' => 'refusée']);

            $autresActives = $reservation->annonce->reservations()
                ->whereNotIn('statut', ['refusée', 'annulée'])
                ->where('id', '!=', $reservation->id)
                ->exists();

            if (!$autresActives) {
                $reservation->annonce->update(['statut' => 'disponible']);
            }
        });

        $reservation->load(['annonce', 'acheteur']);
        $reservation->acheteur->notify(new ReservationRefusee($reservation));
        Mail::to($reservation->acheteur->email)->send(new ReservationRefuseeMail($reservation));

        return back()->with('success', 'Réservation refusée.');
    }

    public function completer(Reservation $reservation)
    {
        if ($reservation->annonce->user_id !== Auth::id()) abort(403);

        DB::transaction(function () use ($reservation) {
            $reservation->update(['statut' => 'complétée']);
        });

        // Invalider le cache de la page d'accueil pour recalculer l'impact
        \Illuminate\Support\Facades\Cache::forget('welcome_impact');

        $reservation->acheteur->notify(new ReservationCompletee($reservation));

        return back()->with('success', 'Échange complété !');
    }

    public function annuler(Reservation $reservation)
    {
        if ($reservation->user_id !== Auth::id()) abort(403);

        DB::transaction(function () use ($reservation) {
            $reservation->update(['statut' => 'annulée']);

            $autresActives = $reservation->annonce->reservations()
                ->whereNotIn('statut', ['refusée', 'annulée'])
                ->where('id', '!=', $reservation->id)
                ->exists();

            if (!$autresActives) {
                $reservation->annonce->update(['statut' => 'disponible']);
            }
        });

        return back()->with('success', 'Réservation annulée.');
    }

    public function mesReservations()
    {
        $user = Auth::user();

        if ($user->isFournisseur()) {
            $reservations = Reservation::with(['annonce', 'acheteur', 'avis'])
                ->whereHas('annonce', fn($q) => $q->where('user_id', $user->id))
                ->latest()->paginate(15);
        } else {
            $reservations = Reservation::with(['annonce', 'annonce.user', 'avis'])
                ->where('user_id', $user->id)->latest()->paginate(15);
        }

        return view('dashboard.mes-reservations', compact('reservations'));
    }
}
