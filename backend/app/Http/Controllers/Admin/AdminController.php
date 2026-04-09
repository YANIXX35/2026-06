<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Annonce;
use App\Models\Signalement;
use App\Models\User;
use App\Models\Reservation;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'utilisateurs'  => User::where('role', '!=', 'admin')->count(),
            'annonces'      => Annonce::count(),
            'reservations'  => Reservation::count(),
            'signalements'  => Signalement::where('statut', 'en_attente')->count(),
            'fournisseurs'  => User::where('role', 'fournisseur')->count(),
            'acheteurs'     => User::where('role', 'acheteur')->count(),
        ];

        $derniersUtilisateurs = User::latest()->take(5)->get();
        $dernieresAnnonces    = Annonce::with('user', 'categorie')->latest()->take(5)->get();
        $signalements         = Signalement::where('statut', 'en_attente')->with('auteur', 'annonce')->latest()->take(5)->get();

        return view('admin.dashboard', compact('stats', 'derniersUtilisateurs', 'dernieresAnnonces', 'signalements'));
    }

    public function utilisateurs(Request $request)
    {
        $query = User::where('role', '!=', 'admin');
        if ($request->filled('role')) $query->where('role', $request->role);
        if ($request->filled('q'))    $query->where('nom', 'like', '%'.$request->q.'%')->orWhere('email', 'like', '%'.$request->q.'%');
        $utilisateurs = $query->latest()->paginate(15);
        return view('admin.utilisateurs', compact('utilisateurs'));
    }

    public function suspendre(User $user)
    {
        $user->update(['statut' => 'suspendu']);
        return back()->with('success', 'Compte suspendu.');
    }

    public function activer(User $user)
    {
        $user->update(['statut' => 'actif']);
        return back()->with('success', 'Compte activé.');
    }

    public function annonces(Request $request)
    {
        $annonces = Annonce::with('user', 'categorie')->latest()->paginate(20);
        return view('admin.annonces', compact('annonces'));
    }

    public function supprimerAnnonce(Annonce $annonce)
    {
        $annonce->update(['statut' => 'supprimé']);
        return back()->with('success', 'Annonce supprimée.');
    }

    public function signalements(Request $request)
    {
        $signalements = Signalement::with('auteur', 'annonce', 'userSignale')->latest()->paginate(20);
        return view('admin.signalements', compact('signalements'));
    }

    public function traiterSignalement(Signalement $signalement)
    {
        $signalement->update(['statut' => 'traité']);
        return back()->with('success', 'Signalement traité.');
    }
}
