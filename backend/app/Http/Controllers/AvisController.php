<?php

namespace App\Http\Controllers;

use App\Models\Avis;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AvisController extends Controller
{
    public function store(Request $request, Reservation $reservation)
    {
        if ($reservation->user_id !== Auth::id()) abort(403);
        if ($reservation->statut !== 'complétée') return back()->with('error', 'Échange non complété.');
        if ($reservation->avis) return back()->with('error', 'Avis déjà soumis.');

        $request->validate([
            'note'        => 'required|integer|min:1|max:5',
            'commentaire' => 'nullable|string|max:500',
        ]);

        Avis::create([
            'reservation_id' => $reservation->id,
            'user_id'        => Auth::id(),
            'fournisseur_id' => $reservation->annonce->user_id,
            'note'           => $request->note,
            'commentaire'    => $request->commentaire,
        ]);

        $fournisseur = $reservation->annonce->user;
        $fournisseur->update(['note_moyenne' => $fournisseur->avisRecus()->avg('note')]);

        return back()->with('success', 'Merci pour votre avis !');
    }
}
