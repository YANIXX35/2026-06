<?php

namespace App\Http\Controllers;

use App\Models\Annonce;
use App\Models\Reservation;
use App\Notifications\ReservationAcceptee;
use App\Notifications\ReservationRefusee;
use App\Notifications\ReservationCompletee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReservationController extends Controller
{
    public function store(Request $request, Annonce $annonce)
    {
        $request->validate([
            'quantite_demandee'       => 'required|numeric|min:0.01',
            'message'                 => 'nullable|string|max:500',
            'date_collecte_souhaitee' => 'nullable|date|after:now',
        ]);

        Reservation::create([
            'annonce_id'              => $annonce->id,
            'user_id'                 => Auth::id(),
            'quantite_demandee'       => $request->quantite_demandee,
            'message'                 => $request->message,
            'date_collecte_souhaitee' => $request->date_collecte_souhaitee,
            'statut'                  => 'en_attente',
        ]);

        $annonce->update(['statut' => 'reservé']);
        return redirect()->route('annonces.show', $annonce)->with('success', 'Réservation envoyée !');
    }

    public function accepter(Reservation $reservation)
    {
        if ($reservation->annonce->user_id !== Auth::id()) abort(403);
        $reservation->update(['statut' => 'acceptée']);
        $reservation->acheteur->notify(new ReservationAcceptee($reservation));
        return back()->with('success', 'Réservation acceptée.');
    }

    public function refuser(Reservation $reservation)
    {
        if ($reservation->annonce->user_id !== Auth::id()) abort(403);
        $reservation->update(['statut' => 'refusée']);
        $reservation->annonce->update(['statut' => 'disponible']);
        $reservation->acheteur->notify(new ReservationRefusee($reservation));
        return back()->with('success', 'Réservation refusée.');
    }

    public function completer(Reservation $reservation)
    {
        if ($reservation->annonce->user_id !== Auth::id()) abort(403);
        $reservation->update(['statut' => 'complétée']);
        $reservation->acheteur->notify(new ReservationCompletee($reservation));
        return back()->with('success', 'Échange complété !');
    }

    public function annuler(Reservation $reservation)
    {
        if ($reservation->user_id !== Auth::id()) abort(403);
        $reservation->update(['statut' => 'annulée']);
        $reservation->annonce->update(['statut' => 'disponible']);
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
