<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AnnonceController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\AvisController;
use App\Http\Controllers\SignalementController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CommandeController;
use App\Http\Controllers\CategorieController;
use App\Http\Controllers\SocialAuthController;
use App\Http\Controllers\PaymentController;

// ─── PAGE D'ACCUEIL ────────────────────────────────────────────
Route::get('/', \App\Http\Controllers\WelcomeController::class)->name('home');

// ─── PAGES STATIQUES ───────────────────────────────────────────
Route::get('/comment-ca-marche', fn() => view('pages.comment-ca-marche'))->name('comment-ca-marche');
Route::get('/contact', fn() => view('pages.contact'))->name('contact');
Route::post('/contact', [App\Http\Controllers\ContactController::class, 'send'])->name('contact.send');
Route::get('/mentions-legales', fn() => view('pages.mentions-legales'))->name('mentions-legales');
Route::get('/confidentialite', fn() => view('pages.confidentialite'))->name('confidentialite');
Route::get('/cgu', fn() => view('pages.cgu'))->name('cgu');
Route::post('/newsletter', [App\Http\Controllers\NewsletterController::class, 'store'])->name('newsletter.store')->middleware('throttle:10,1');

// ─── AUTHENTIFICATION ──────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/inscription', [AuthController::class, 'showInscription'])->name('inscription');
    Route::post('/inscription', [AuthController::class, 'inscrire'])->name('inscrire')->middleware('throttle:10,1');
    Route::get('/inscription/verifier-email', [AuthController::class, 'showOtpInscriptionForm'])->name('inscription.otp.form');
    Route::post('/inscription/verifier-email', [AuthController::class, 'verifierOtpInscription'])->name('inscription.otp.verify');
    Route::get('/connexion', [AuthController::class, 'showConnexion'])->name('connexion');
    Route::post('/connexion', [AuthController::class, 'connecter'])->name('connecter')->middleware('throttle:20,1');

    // ─── MOT DE PASSE OUBLIÉ (OTP) ─────────────────────────────
    Route::get('/mot-de-passe-oublie', [PasswordResetController::class, 'showEmailForm'])->name('password.email.form');
    Route::post('/mot-de-passe-oublie', [PasswordResetController::class, 'sendOtp'])->name('password.otp.send');
    Route::get('/verifier-otp', [PasswordResetController::class, 'showOtpForm'])->name('password.otp.form');
    Route::post('/verifier-otp', [PasswordResetController::class, 'verifyOtp'])->name('password.otp.verify');
    Route::get('/nouveau-mot-de-passe', [PasswordResetController::class, 'showNewPasswordForm'])->name('password.new.form');
    Route::post('/nouveau-mot-de-passe', [PasswordResetController::class, 'resetPassword'])->name('password.reset');
});

Route::post('/deconnexion', [AuthController::class, 'deconnecter'])->name('deconnecter')->middleware('auth');

// ─── SOCIAL AUTH ───────────────────────────────────────────────
Route::get('/auth/{provider}/redirect', [SocialAuthController::class, 'redirect'])->name('social.redirect');
Route::get('/auth/{provider}/callback', [SocialAuthController::class, 'callback'])->name('social.callback');

// ─── PROFIL PUBLIC FOURNISSEUR ─────────────────────────────────
Route::get('/fournisseurs/{user}', [ProfilController::class, 'show'])->name('profil.show');

// ─── ANNONCES (publiques + routes fixes en premier) ────────────
Route::get('/annonces', [AnnonceController::class, 'index'])->name('annonces.index');
// Routes fixes AVANT le wildcard {annonce} pour éviter les conflits
Route::get('/annonces/publier', [AnnonceController::class, 'create'])->middleware('auth')->name('annonces.create');
Route::get('/annonces/{annonce}', [AnnonceController::class, 'show'])->name('annonces.show');

// ─── ZONE CONNECTÉE ────────────────────────────────────────────
Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');

    // Profil
    Route::get('/profil/modifier', [ProfilController::class, 'edit'])->name('profil.edit');
    Route::put('/profil/modifier', [ProfilController::class, 'update'])->name('profil.update');

    // Annonces (gestion)
    Route::post('/annonces', [AnnonceController::class, 'store'])->name('annonces.store');
    Route::get('/annonces/{annonce}/modifier', [AnnonceController::class, 'edit'])->name('annonces.edit');
    Route::put('/annonces/{annonce}', [AnnonceController::class, 'update'])->name('annonces.update');
    Route::delete('/annonces/{annonce}', [AnnonceController::class, 'destroy'])->name('annonces.destroy');
    Route::get('/mes-annonces', [AnnonceController::class, 'mesAnnonces'])->name('annonces.mes-annonces');

    // Réservations
    Route::post('/annonces/{annonce}/reserver', [ReservationController::class, 'store'])->name('reservations.store');
    Route::post('/reservations/{reservation}/accepter', [ReservationController::class, 'accepter'])->name('reservations.accepter');
    Route::post('/reservations/{reservation}/refuser', [ReservationController::class, 'refuser'])->name('reservations.refuser');
    Route::post('/reservations/{reservation}/completer', [ReservationController::class, 'completer'])->name('reservations.completer');
    Route::post('/reservations/{reservation}/annuler', [ReservationController::class, 'annuler'])->name('reservations.annuler');
    Route::get('/mes-reservations', [ReservationController::class, 'mesReservations'])->name('reservations.mes-reservations');

    // Panier
    Route::get('/panier', [CartController::class, 'index'])->name('cart.index');
    Route::post('/annonces/{annonce}/panier', [CartController::class, 'ajouter'])->name('cart.ajouter');
    Route::patch('/panier/{cartItem}', [CartController::class, 'mettreAJour'])->name('cart.update');
    Route::delete('/panier/{cartItem}', [CartController::class, 'supprimer'])->name('cart.remove');
    Route::delete('/panier', [CartController::class, 'vider'])->name('cart.vider');

    // Paiement
    Route::post('/paiement/initier', [PaymentController::class, 'initier'])->name('paiement.initier');
    Route::get('/paiement', [PaymentController::class, 'show'])->name('paiement.show');
    Route::post('/paiement/confirmer', [PaymentController::class, 'confirmer'])->name('paiement.confirmer');
    Route::get('/paiement/succes/{commande}', [PaymentController::class, 'succes'])->name('paiement.succes');

    // Commandes
    Route::get('/commandes', [CommandeController::class, 'index'])->name('commandes.index');
    Route::get('/commandes/{commande}', [CommandeController::class, 'show'])->name('commandes.show');
    Route::post('/commandes', [CommandeController::class, 'passer'])->name('commandes.passer');
    Route::post('/commandes/{commande}/annuler', [CommandeController::class, 'annuler'])->name('commandes.annuler');
    // Commandes — espace fournisseur
    Route::get('/fournisseur/commandes', [CommandeController::class, 'indexFournisseur'])->name('commandes.fournisseur');
    Route::post('/commandes/items/{item}/accepter', [CommandeController::class, 'accepterItem'])->name('commandes.items.accepter');
    Route::post('/commandes/items/{item}/refuser', [CommandeController::class, 'refuserItem'])->name('commandes.items.refuser');

    // Messagerie
    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/{conversation}', [MessageController::class, 'show'])->name('messages.show');
    Route::post('/messages/{conversation}', [MessageController::class, 'envoyer'])->name('messages.envoyer');
    Route::get('/messages/ouvrir/{user}', [MessageController::class, 'ouvrirOuCreer'])->name('messages.ouvrir');

    // Offres (négociation de prix)
    Route::post('/messages/{conversation}/offres', [\App\Http\Controllers\OffreController::class, 'store'])->name('offres.store');
    Route::post('/offres/{offre}/accepter', [\App\Http\Controllers\OffreController::class, 'accepter'])->name('offres.accepter');
    Route::post('/offres/{offre}/refuser', [\App\Http\Controllers\OffreController::class, 'refuser'])->name('offres.refuser');

    // Avis
    Route::post('/reservations/{reservation}/avis', [AvisController::class, 'store'])->name('avis.store');

    // Signalements
    Route::post('/signaler', [SignalementController::class, 'store'])->name('signalements.store');

    // Abonnements catégories (alertes)
    Route::post('/abonnements/{categorie}', [\App\Http\Controllers\AbonnementController::class, 'toggle'])->name('abonnements.toggle');
});

// ─── NOTIFICATIONS ─────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/notifications', function () {
        $notifications = Auth::user()->notifications()->paginate(20);
        Auth::user()->unreadNotifications->markAsRead();
        return view('notifications.index', compact('notifications'));
    })->name('notifications.index');

    Route::post('/notifications/mark-all-read', function () {
        Auth::user()->unreadNotifications->markAsRead();
        return back();
    })->name('notifications.markAllRead');

    Route::post('/notifications/{id}/mark-read', function ($id) {
        $notif = Auth::user()->notifications()->find($id);
        if ($notif) $notif->markAsRead();
        return response()->json(['ok' => true]);
    })->name('notifications.markRead');
});

// ─── BLOG RSS ──────────────────────────────────────────────────
Route::get('/blog', [\App\Http\Controllers\BlogController::class, 'index'])->name('blog');
Route::post('/blog/refresh', [\App\Http\Controllers\BlogController::class, 'refresh'])->name('blog.refresh')->middleware('auth');

// ─── CHAT IA ───────────────────────────────────────────────────
Route::post('/chat', [\App\Http\Controllers\ChatController::class, 'chat'])
    ->name('chat')
    ->middleware(['throttle:30,1']);

// ─── ADMIN ──────────────────────────────────────────────────────
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/utilisateurs', [AdminController::class, 'utilisateurs'])->name('utilisateurs');
    Route::post('/utilisateurs', [AdminController::class, 'storeUtilisateur'])->name('utilisateurs.store');
    Route::put('/utilisateurs/{user}', [AdminController::class, 'updateUtilisateur'])->name('utilisateurs.update');
    Route::delete('/utilisateurs/{user}', [AdminController::class, 'destroyUtilisateur'])->name('utilisateurs.destroy');
    Route::post('/utilisateurs/{user}/reset-password', [AdminController::class, 'resetPasswordUtilisateur'])->name('utilisateurs.reset-password');
    Route::post('/utilisateurs/{user}/suspendre', [AdminController::class, 'suspendre'])->name('suspendre');
    Route::post('/utilisateurs/{user}/activer', [AdminController::class, 'activer'])->name('activer');
    Route::get('/annonces', [AdminController::class, 'annonces'])->name('annonces');
    Route::delete('/annonces/{annonce}', [AdminController::class, 'supprimerAnnonce'])->name('supprimer-annonce');
    Route::get('/signalements', [AdminController::class, 'signalements'])->name('signalements');
    Route::post('/signalements/{signalement}/traiter', [AdminController::class, 'traiterSignalement'])->name('traiter-signalement');
    // Catégories
    Route::get('/categories', [CategorieController::class, 'index'])->name('categories.index');
    Route::get('/categories/create', [CategorieController::class, 'create'])->name('categories.create');
    Route::post('/categories', [CategorieController::class, 'store'])->name('categories.store');
    Route::get('/categories/{categorie}/edit', [CategorieController::class, 'edit'])->name('categories.edit');
    Route::put('/categories/{categorie}', [CategorieController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{categorie}', [CategorieController::class, 'destroy'])->name('categories.destroy');
});
