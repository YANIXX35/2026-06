<?php

namespace App\Http\Controllers;

use App\Mail\BienvenueMail;
use App\Mail\OtpInscriptionMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
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
        $rules = [
            'nom'       => 'required|string|max:100',
            'prenom'    => 'required|string|max:100',
            'email'     => 'required|email|unique:users',
            'telephone' => 'nullable|string|max:20',
            'password'  => 'required|string|min:8|confirmed',
            'role'      => 'required|in:fournisseur,acheteur',
        ];

        if ($request->role === 'fournisseur') {
            $rules['type_structure'] = 'required|string';
            $rules['nom_structure']  = 'required|string|max:200';
        }

        $request->validate($rules);

        // Stocker les données en session (mot de passe haché, jamais en clair)
        $pending = [
            'nom'           => $request->nom,
            'prenom'        => $request->prenom,
            'email'         => $request->email,
            'telephone'     => $request->filled('telephone') ? $request->telephone : null,
            'password_hash' => Hash::make($request->password),
            'role'          => $request->role,
            'type_structure'=> $request->type_structure ?? null,
            'nom_structure' => $request->nom_structure ?? null,
            'type_acheteur' => $request->type_acheteur ?? 'particulier',
        ];

        session(['inscription_pending' => $pending, 'inscription_email' => $request->email]);

        // Générer et envoyer l'OTP
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        DB::table('password_reset_otps')->where('email', $request->email)->delete();
        DB::table('password_reset_otps')->insert([
            'email'      => $request->email,
            'otp'        => $otp,
            'expires_at' => now()->addMinutes(10),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $mailSent = false;
        try {
            Mail::to($request->email)->send(new OtpInscriptionMail($otp, $request->prenom));
            $mailSent = true;
        } catch (\Exception $e) {
            \Log::error('OTP inscription mail failed: ' . $e->getMessage());
        }

        if (!$mailSent && config('app.debug')) {
            return redirect()->route('inscription.otp.form')
                ->with('warning', "⚠️ Email non envoyé (SMTP non configuré). Votre code OTP de test : <strong>{$otp}</strong>");
        }

        return redirect()->route('inscription.otp.form')
            ->with('success', 'Un code à 6 chiffres a été envoyé à ' . $request->email . '. Entrez-le pour activer votre compte.');
    }

    public function showOtpInscriptionForm()
    {
        if (!session('inscription_pending')) {
            return redirect()->route('inscription');
        }
        return view('auth.verifier-otp-inscription');
    }

    public function verifierOtpInscription(Request $request)
    {
        $request->validate(['otp' => 'required|digits:6']);

        $pending = session('inscription_pending');
        $email   = session('inscription_email');

        if (!$pending || !$email) {
            return redirect()->route('inscription');
        }

        $record = DB::table('password_reset_otps')
            ->where('email', $email)
            ->where('otp', $request->otp)
            ->first();

        if (!$record) {
            return back()->withErrors(['otp' => 'Code incorrect. Veuillez réessayer.']);
        }

        if (now()->isAfter($record->expires_at)) {
            DB::table('password_reset_otps')->where('email', $email)->delete();
            return back()->withErrors(['otp' => 'Ce code a expiré. Recommencez l\'inscription.']);
        }

        // OTP valide → créer le compte
        DB::table('password_reset_otps')->where('email', $email)->delete();

        $userData = [
            'nom'      => $pending['nom'],
            'prenom'   => $pending['prenom'],
            'email'    => $pending['email'],
            'telephone'=> $pending['telephone'],
            'password' => $pending['password_hash'],
            'role'     => $pending['role'],
            'statut'   => 'actif',
        ];

        if ($pending['role'] === 'fournisseur') {
            $userData['type_structure'] = $pending['type_structure'];
            $userData['nom_structure']  = $pending['nom_structure'];
        } else {
            $userData['type_acheteur'] = $pending['type_acheteur'];
        }

        $user = User::create($userData);

        session()->forget(['inscription_pending', 'inscription_email']);

        // Email de bienvenue
        try {
            Mail::to($user->email)->send(new BienvenueMail($user));
        } catch (\Exception $e) {
            \Log::warning('Email bienvenue non envoyé : ' . $e->getMessage());
        }

        Auth::login($user);

        return redirect()->route('dashboard')->with('success', '🎉 Bienvenue sur AntiGaspiCI ! Votre email a été confirmé.');
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

        // Une seule requête DB : on charge l'utilisateur une fois et on vérifie nous-mêmes
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            \Illuminate\Support\Facades\Log::warning('Échec connexion', [
                'email'       => $request->email,
                'user_exists' => $user ? 'oui' : 'non',
                'statut'      => $user?->statut,
            ]);
            return back()->withErrors(['email' => 'Email ou mot de passe incorrect.'])->withInput();
        }

        if ($user->statut === 'suspendu') {
            return back()
                ->withErrors(['email' => 'Votre compte a été suspendu. Contactez l\'administrateur.'])
                ->withInput();
        }

        // Connexion directe sans re-requêter la DB
        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        \Illuminate\Support\Facades\Log::info('Connexion réussie', ['email' => $user->email, 'role' => $user->role]);

        if ($user->role === 'admin') return redirect()->route('admin.dashboard');
        return redirect()->route('dashboard');
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
            $cacheKey = 'dashboard_fournisseur_' . $user->id;
            $dashData = Cache::remember($cacheKey, 300, function () use ($user) {
                $annonces = $user->annonces()->with('reservations')->get();
                $reservationsCompletes = \App\Models\Reservation::with('annonce')
                    ->whereHas('annonce', fn($q) => $q->where('user_id', $user->id))
                    ->where('statut', 'complétée')->get();

                $kgSauves       = $reservationsCompletes->sum('quantite_demandee');
                $co2Evite       = round($kgSauves * 2.5, 1);
                $revenusGeneres = $reservationsCompletes->sum(fn($r) => $r->annonce ? $r->annonce->prix * $r->quantite_demandee : 0);

                $annonceIds = $annonces->pluck('id');
                $reservationsParJour = \App\Models\Reservation::whereIn('annonce_id', $annonceIds)
                    ->where('created_at', '>=', now()->subDays(6)->startOfDay())
                    ->selectRaw('DATE(created_at) as jour, count(*) as total')
                    ->groupBy('jour')
                    ->pluck('total', 'jour');

                return [
                    'annonces'              => $annonces,
                    'reservationsCompletes' => $reservationsCompletes,
                    'kgSauves'              => $kgSauves,
                    'co2Evite'              => $co2Evite,
                    'revenusGeneres'        => $revenusGeneres,
                    'stats' => [
                        'annonces_actives'   => $annonces->where('statut', 'disponible')->count(),
                        'reservations_att'   => \App\Models\Reservation::whereHas('annonce', fn($q) => $q->where('user_id', $user->id))->where('statut', 'en_attente')->count(),
                        'echanges_completes' => $reservationsCompletes->count(),
                        'note_moyenne'       => round($user->note_moyenne ?? 0, 1),
                        'total_vues'         => $annonces->sum('vues'),
                        'messages_non_lus'   => \App\Models\Message::where('user_id', '!=', $user->id)
                            ->whereHas('conversation', fn($q) => $q->where('user_1_id', $user->id)->orWhere('user_2_id', $user->id))
                            ->where('lu', false)->count(),
                    ],
                    'dernieresReservations' => \App\Models\Reservation::with(['annonce', 'acheteur'])
                        ->whereHas('annonce', fn($q) => $q->where('user_id', $user->id))
                        ->latest()->take(5)->get(),
                    'dernieresAnnonces'     => $user->annonces()->with('categorie')->latest()->take(5)->get(),
                    'chartLabels'           => collect(range(6, 0))->map(fn($d) => now()->subDays($d)->format('d/m'))->toArray(),
                    'chartData'             => collect(range(6, 0))->map(
                        fn($d) => (int) ($reservationsParJour[now()->subDays($d)->toDateString()] ?? 0)
                    )->toArray(),
                ];
            });

            $annonces              = $dashData['annonces'];
            $reservationsCompletes = $dashData['reservationsCompletes'];
            $kgSauves              = $dashData['kgSauves'];
            $co2Evite              = $dashData['co2Evite'];
            $revenusGeneres        = $dashData['revenusGeneres'];
            $stats                 = $dashData['stats'];
            $impact = [
                'kg_sauves'      => round($kgSauves, 1),
                'co2_evite'      => $co2Evite,
                'revenus_generes'=> number_format($revenusGeneres, 0, ',', ' '),
                'repas_equiv'    => round($kgSauves / 0.5),
            ];
            $dernieresReservations = $dashData['dernieresReservations'];
            $dernieresAnnonces     = $dashData['dernieresAnnonces'];
            $chartLabels           = $dashData['chartLabels'];
            $chartData             = $dashData['chartData'];

            return view('dashboard.fournisseur', compact('user', 'stats', 'impact', 'dernieresReservations', 'dernieresAnnonces', 'chartLabels', 'chartData'));
        }

        // Acheteur — cache 5 minutes
        $cacheKey = 'dashboard_acheteur_' . $user->id;
        $dashData = Cache::remember($cacheKey, 300, function () use ($user) {
            $reservationsCompletes = $user->reservations()->with('annonce')->where('statut', 'complétée')->get();
            $kgSauves    = $reservationsCompletes->sum('quantite_demandee');
            $co2Evite    = round($kgSauves * 2.5, 1);
            $argentEco   = $reservationsCompletes->sum(fn($r) => $r->annonce ? $r->annonce->prix * $r->quantite_demandee * 0.4 : 0);

            return [
                'reservationsCompletes' => $reservationsCompletes,
                'kgSauves'    => $kgSauves,
                'co2Evite'    => $co2Evite,
                'argentEco'   => $argentEco,
                'stats' => [
                    'reservations_total'   => $user->reservations()->count(),
                    'reservations_att'     => $user->reservations()->where('statut', 'en_attente')->count(),
                    'echanges_completes'   => $reservationsCompletes->count(),
                    'messages_non_lus'     => \App\Models\Message::where('user_id', '!=', $user->id)
                        ->whereHas('conversation', fn($q) => $q->where('user_1_id', $user->id)->orWhere('user_2_id', $user->id))
                        ->where('lu', false)->count(),
                    'annonces_disponibles' => \App\Models\Annonce::where('statut', 'disponible')->count(),
                ],
                'categoriesAbonnees'    => \App\Models\AbonnementCategorie::where('user_id', $user->id)->pluck('categorie_id'),
                'dernieresReservations' => $user->reservations()->with(['annonce', 'annonce.user'])->latest()->take(5)->get(),
                'annoncesSuggestions'   => \App\Models\Annonce::with(['user', 'categorie', 'photoPrincipale'])->where('statut', 'disponible')->latest()->take(4)->get(),
                'toutesCategories'      => \App\Models\Categorie::all(),
            ];
        });

        $reservationsCompletes = $dashData['reservationsCompletes'];
        $kgSauves    = $dashData['kgSauves'];
        $co2Evite    = $dashData['co2Evite'];
        $argentEco   = $dashData['argentEco'];
        $stats       = $dashData['stats'];
        $impact = [
            'kg_sauves'    => round($kgSauves, 1),
            'co2_evite'    => $co2Evite,
            'argent_eco'   => number_format($argentEco, 0, ',', ' '),
            'repas_equiv'  => round($kgSauves / 0.5),
        ];
        $categoriesAbonnees    = $dashData['categoriesAbonnees'];
        $dernieresReservations = $dashData['dernieresReservations'];
        $annoncesSuggestions   = $dashData['annoncesSuggestions'];
        $toutesCategories      = $dashData['toutesCategories'];

        return view('dashboard.acheteur', compact('user', 'stats', 'impact', 'categoriesAbonnees', 'toutesCategories', 'dernieresReservations', 'annoncesSuggestions'));
    }
}
