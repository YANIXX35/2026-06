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

// ─── PAGE D'ACCUEIL ────────────────────────────────────────────
Route::get('/', fn() => view('welcome'))->name('home');

// ─── PAGES STATIQUES ───────────────────────────────────────────
Route::get('/comment-ca-marche', fn() => view('pages.comment-ca-marche'))->name('comment-ca-marche');
Route::get('/contact', fn() => view('pages.contact'))->name('contact');

// ─── AUTHENTIFICATION ──────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/inscription', [AuthController::class, 'showInscription'])->name('inscription');
    Route::post('/inscription', [AuthController::class, 'inscrire'])->name('inscrire');
    Route::get('/connexion', [AuthController::class, 'showConnexion'])->name('connexion');
    Route::post('/connexion', [AuthController::class, 'connecter'])->name('connecter');

    // ─── MOT DE PASSE OUBLIÉ (OTP) ─────────────────────────────
    Route::get('/mot-de-passe-oublie', [PasswordResetController::class, 'showEmailForm'])->name('password.email.form');
    Route::post('/mot-de-passe-oublie', [PasswordResetController::class, 'sendOtp'])->name('password.otp.send');
    Route::get('/verifier-otp', [PasswordResetController::class, 'showOtpForm'])->name('password.otp.form');
    Route::post('/verifier-otp', [PasswordResetController::class, 'verifyOtp'])->name('password.otp.verify');
    Route::get('/nouveau-mot-de-passe', [PasswordResetController::class, 'showNewPasswordForm'])->name('password.new.form');
    Route::post('/nouveau-mot-de-passe', [PasswordResetController::class, 'resetPassword'])->name('password.reset');
});

Route::post('/deconnexion', [AuthController::class, 'deconnecter'])->name('deconnecter')->middleware('auth');

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

    // Messagerie
    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/{conversation}', [MessageController::class, 'show'])->name('messages.show');
    Route::post('/messages/{conversation}', [MessageController::class, 'envoyer'])->name('messages.envoyer');
    Route::get('/messages/ouvrir/{user}', [MessageController::class, 'ouvrirOuCreer'])->name('messages.ouvrir');

    // Avis
    Route::post('/reservations/{reservation}/avis', [AvisController::class, 'store'])->name('avis.store');

    // Signalements
    Route::post('/signaler', [SignalementController::class, 'store'])->name('signalements.store');
});

// ─── BLOG RSS ──────────────────────────────────────────────────
Route::get('/blog', [\App\Http\Controllers\BlogController::class, 'index'])->name('blog');
Route::post('/blog/refresh', [\App\Http\Controllers\BlogController::class, 'refresh'])->name('blog.refresh')->middleware('auth');

// ─── CHAT IA ───────────────────────────────────────────────────
Route::post('/chat', [\App\Http\Controllers\ChatController::class, 'chat'])->name('chat');

// ─── ADMIN ──────────────────────────────────────────────────────
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/utilisateurs', [AdminController::class, 'utilisateurs'])->name('utilisateurs');
    Route::post('/utilisateurs/{user}/suspendre', [AdminController::class, 'suspendre'])->name('suspendre');
    Route::post('/utilisateurs/{user}/activer', [AdminController::class, 'activer'])->name('activer');
    Route::get('/annonces', [AdminController::class, 'annonces'])->name('annonces');
    Route::delete('/annonces/{annonce}', [AdminController::class, 'supprimerAnnonce'])->name('supprimer-annonce');
    Route::get('/signalements', [AdminController::class, 'signalements'])->name('signalements');
    Route::post('/signalements/{signalement}/traiter', [AdminController::class, 'traiterSignalement'])->name('traiter-signalement');
});
