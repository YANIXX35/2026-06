<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Annonce;
use App\Models\ChatLog;
use App\Models\Signalement;
use App\Models\User;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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
            'questions_chatbot' => ChatLog::count(),
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
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(fn($sq) => $sq->where('nom', 'like', "%$q%")->orWhere('prenom', 'like', "%$q%")->orWhere('email', 'like', "%$q%"));
        }
        $utilisateurs = $query->latest()->paginate(15);
        return view('admin.utilisateurs', compact('utilisateurs'));
    }

    public function storeUtilisateur(Request $request)
    {
        $request->validate([
            'nom'      => 'required|string|max:100',
            'prenom'   => 'required|string|max:100',
            'email'    => 'required|email|unique:users,email',
            'telephone'=> 'nullable|string|max:20',
            'role'     => 'required|in:fournisseur,acheteur,admin',
            'password' => 'required|string|min:8',
        ], [
            'email.unique'    => 'Cet email est déjà utilisé.',
            'password.min'    => 'Le mot de passe doit contenir au moins 8 caractères.',
        ]);

        User::create([
            'nom'       => $request->nom,
            'prenom'    => $request->prenom,
            'email'     => $request->email,
            'telephone' => $request->telephone,
            'role'      => $request->role,
            'password'  => $request->password,
            'statut'    => 'actif',
        ]);

        return redirect()->route('admin.utilisateurs')->with('success', "Utilisateur {$request->prenom} {$request->nom} créé avec succès.");
    }

    public function updateUtilisateur(Request $request, User $user)
    {
        $request->validate([
            'nom'      => 'required|string|max:100',
            'prenom'   => 'required|string|max:100',
            'email'    => 'required|email|unique:users,email,'.$user->id,
            'telephone'=> 'nullable|string|max:20',
            'role'     => 'required|in:fournisseur,acheteur,admin',
            'statut'   => 'required|in:actif,suspendu,en_attente',
        ], [
            'email.unique' => 'Cet email est déjà utilisé par un autre compte.',
        ]);

        $user->update([
            'nom'       => $request->nom,
            'prenom'    => $request->prenom,
            'email'     => $request->email,
            'telephone' => $request->telephone,
            'role'      => $request->role,
            'statut'    => $request->statut,
        ]);

        return redirect()->route('admin.utilisateurs')->with('success', "Compte de {$user->prenom} {$user->nom} mis à jour.");
    }

    public function destroyUtilisateur(User $user)
    {
        if ($user->id === Auth::id()) {
            return redirect()->route('admin.utilisateurs')->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }

        $nom = $user->prenom.' '.$user->nom;
        $user->delete();

        return redirect()->route('admin.utilisateurs')->with('success', "Utilisateur {$nom} supprimé.");
    }

    public function resetPasswordUtilisateur(Request $request, User $user)
    {
        $request->validate([
            'password' => 'required|string|min:8',
        ], [
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
        ]);

        $user->password = $request->password;
        $user->save();

        return redirect()->route('admin.utilisateurs')->with('success', "Mot de passe de {$user->prenom} {$user->nom} réinitialisé.");
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
