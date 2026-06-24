<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Annonce;
use App\Models\Commande;
use App\Models\Signalement;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminApiController extends Controller
{
    private function checkAdmin()
    {
        if (Auth::user()?->role !== 'admin') {
            abort(response()->json(['message' => 'Accès réservé aux administrateurs.'], 403));
        }
    }

    // ── DASHBOARD ─────────────────────────────────────────────────────────
    public function dashboard()
    {
        $this->checkAdmin();

        return response()->json([
            'stats' => [
                'utilisateurs'  => User::where('role', '!=', 'admin')->count(),
                'annonces'      => Annonce::count(),
                'commandes'     => Commande::count(),
                'signalements'  => Signalement::where('statut', 'en_attente')->count(),
                'fournisseurs'  => User::where('role', 'fournisseur')->count(),
                'acheteurs'     => User::where('role', 'acheteur')->count(),
            ],
            'derniers_utilisateurs' => User::latest()->take(5)->get(['id', 'nom', 'prenom', 'email', 'role', 'statut']),
            'dernieres_annonces'    => Annonce::with('user:id,nom,prenom', 'categorie:id,nom')
                ->latest()->take(5)
                ->get(['id', 'titre', 'statut', 'type_offre', 'prix', 'user_id', 'categorie_id', 'created_at']),
            'signalements_recents'  => Signalement::with('auteur:id,nom,prenom', 'annonce:id,titre')
                ->where('statut', 'en_attente')->latest()->take(5)->get(),
        ]);
    }

    // ── UTILISATEURS ──────────────────────────────────────────────────────
    public function utilisateurs(Request $request)
    {
        $this->checkAdmin();

        $query = User::where('role', '!=', 'admin');

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(fn($sq) =>
                $sq->where('nom', 'ilike', "%$q%")
                   ->orWhere('prenom', 'ilike', "%$q%")
                   ->orWhere('email', 'ilike', "%$q%")
            );
        }

        return response()->json($query->latest()->paginate(20));
    }

    public function suspendreUtilisateur(User $user)
    {
        $this->checkAdmin();
        $user->update(['statut' => 'suspendu']);
        return response()->json(['message' => 'Compte suspendu.', 'user' => $user]);
    }

    public function activerUtilisateur(User $user)
    {
        $this->checkAdmin();
        $user->update(['statut' => 'actif']);
        return response()->json(['message' => 'Compte activé.', 'user' => $user]);
    }

    public function supprimerUtilisateur(User $user)
    {
        $this->checkAdmin();

        if ($user->id === Auth::id()) {
            return response()->json(['message' => 'Impossible de supprimer votre propre compte.'], 422);
        }

        $nom = $user->prenom . ' ' . $user->nom;
        $user->delete();
        return response()->json(['message' => "Utilisateur $nom supprimé."]);
    }

    // ── ANNONCES ──────────────────────────────────────────────────────────
    public function annonces(Request $request)
    {
        $this->checkAdmin();

        $query = Annonce::with('user:id,nom,prenom', 'categorie:id,nom');

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where('titre', 'ilike', "%$q%");
        }
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        return response()->json($query->latest()->paginate(20));
    }

    public function supprimerAnnonce(Annonce $annonce)
    {
        $this->checkAdmin();
        $annonce->update(['statut' => 'supprimé']);
        return response()->json(['message' => 'Annonce supprimée.']);
    }

    // ── SIGNALEMENTS ──────────────────────────────────────────────────────
    public function signalements(Request $request)
    {
        $this->checkAdmin();

        $query = Signalement::with('auteur:id,nom,prenom,email', 'annonce:id,titre', 'userSignale:id,nom,prenom,email');

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        return response()->json($query->latest()->paginate(20));
    }

    public function traiterSignalement(Signalement $signalement)
    {
        $this->checkAdmin();
        $signalement->update(['statut' => 'traité']);
        return response()->json(['message' => 'Signalement traité.', 'signalement' => $signalement]);
    }

    // ── COMMANDES ─────────────────────────────────────────────────────────
    public function commandes(Request $request)
    {
        $this->checkAdmin();

        $query = Commande::with('user:id,nom,prenom,email');

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        return response()->json($query->latest()->paginate(20));
    }
}
