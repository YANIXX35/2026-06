<?php

namespace App\Http\Controllers;

use App\Mail\BienvenueMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    public function showInscription()
    {
        return view('auth.inscription');
    }

    public function inscrire(Request $request)
    {
        $request->validate([
            'nom'       => 'required|string|max:100',
            'prenom'    => 'required|string|max:100',
            'email'     => 'required|email|unique:users',
            'telephone' => 'nullable|string|max:20',
            'password'  => 'required|string|min:8|confirmed',
            'role'      => 'required|in:fournisseur,acheteur',
        ]);

        $data = [
            'nom'       => $request->nom,
            'prenom'    => $request->prenom,
            'email'     => $request->email,
            'telephone' => $request->filled('telephone') ? $request->telephone : null,
            'password'  => Hash::make($request->password),
            'role'      => $request->role,
            'statut'    => 'actif',
        ];

        if ($request->role === 'fournisseur') {
            $request->validate([
                'type_structure' => 'required|string',
                'nom_structure'  => 'required|string|max:200',
            ]);
            $data['type_structure'] = $request->type_structure;
            $data['nom_structure']  = $request->nom_structure;
        } else {
            $data['type_acheteur'] = $request->type_acheteur ?? 'particulier';
        }

        $user = User::create($data);

        // Envoi de l'email de bienvenue
        try {
            Mail::to($user->email)->send(new BienvenueMail($user));
        } catch (\Exception $e) {
            // L'email échoue silencieusement — le compte est créé quand même
            \Log::warning('Email bienvenue non envoyé : ' . $e->getMessage());
        }

        Auth::login($user);

        return redirect()->route('dashboard')->with('success', 'Bienvenue sur AntiGaspiCI ! Un email de confirmation a été envoyé.');
    }

    public function showConnexion()
    {
        return view('auth.connexion');
    }

    public function connecter(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password], $request->remember)) {
            $request->session()->regenerate();
            $user = Auth::user();

            if ($user->role === 'admin') return redirect()->route('admin.dashboard');
            return redirect()->route('dashboard');
        }

        return back()->withErrors(['email' => 'Email ou mot de passe incorrect.'])->withInput();
    }

    public function deconnecter(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    public function dashboard()
    {
        $user = Auth::user();

        if ($user->role === 'admin') return redirect()->route('admin.dashboard');

        if ($user->role === 'fournisseur') {
            $annonces      = $user->annonces()->with('reservations')->get();
            $stats = [
                'annonces_actives'   => $annonces->where('statut', 'disponible')->count(),
                'reservations_att'   => \App\Models\Reservation::whereHas('annonce', fn($q) => $q->where('user_id', $user->id))->where('statut', 'en_attente')->count(),
                'echanges_completes' => \App\Models\Reservation::whereHas('annonce', fn($q) => $q->where('user_id', $user->id))->where('statut', 'complétée')->count(),
                'note_moyenne'       => round($user->note_moyenne ?? 0, 1),
                'total_vues'         => $annonces->sum('vues'),
                'messages_non_lus'   => \App\Models\Message::where('user_id', '!=', $user->id)->whereHas('conversation', fn($q) => $q->where('user_1_id', $user->id)->orWhere('user_2_id', $user->id))->where('lu', false)->count(),
            ];
            $dernieresReservations = \App\Models\Reservation::with(['annonce', 'acheteur'])
                ->whereHas('annonce', fn($q) => $q->where('user_id', $user->id))
                ->latest()->take(5)->get();
            $dernieresAnnonces = $user->annonces()->with('categorie')->latest()->take(5)->get();
            // Réservations par jour (7 derniers jours)
            $annonceIds  = $annonces->pluck('id');
            $chartLabels = collect(range(6, 0))->map(fn($d) => now()->subDays($d)->format('d/m'))->toArray();
            $chartData   = collect(range(6, 0))->map(function ($d) use ($annonceIds) {
                $date = now()->subDays($d)->toDateString();
                return \App\Models\Reservation::whereIn('annonce_id', $annonceIds)
                    ->whereDate('created_at', $date)
                    ->count();
            })->toArray();
            return view('dashboard.fournisseur', compact('user', 'stats', 'dernieresReservations', 'dernieresAnnonces', 'chartLabels', 'chartData'));
        }

        // Acheteur
        $stats = [
            'reservations_total'  => $user->reservations()->count(),
            'reservations_att'    => $user->reservations()->where('statut', 'en_attente')->count(),
            'echanges_completes'  => $user->reservations()->where('statut', 'complétée')->count(),
            'messages_non_lus'    => \App\Models\Message::where('user_id', '!=', $user->id)->whereHas('conversation', fn($q) => $q->where('user_1_id', $user->id)->orWhere('user_2_id', $user->id))->where('lu', false)->count(),
            'annonces_disponibles'=> \App\Models\Annonce::where('statut', 'disponible')->count(),
        ];
        $dernieresReservations = $user->reservations()->with(['annonce', 'annonce.user'])->latest()->take(5)->get();
        $annoncesSuggestions   = \App\Models\Annonce::with(['user', 'categorie'])->where('statut', 'disponible')->latest()->take(4)->get();
        return view('dashboard.acheteur', compact('user', 'stats', 'dernieresReservations', 'annoncesSuggestions'));
    }
}
