<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\AnnonceApiController;
use App\Http\Controllers\Api\CartApiController;
use App\Http\Controllers\Api\CommandeApiController;
use App\Http\Controllers\Api\AdminApiController;
use App\Http\Controllers\Api\FournisseurApiController;

// ── ROUTES PUBLIQUES ──────────────────────────────────────────────
Route::post('/auth/login',        [AuthApiController::class, 'login']);
Route::post('/auth/register',     [AuthApiController::class, 'register']);
Route::post('/auth/verify-otp',   [AuthApiController::class, 'verifierOtp']);
Route::post('/auth/resend-otp',   [AuthApiController::class, 'renvoyerOtp']);

Route::get('/annonces',          [AnnonceApiController::class, 'index']);
Route::get('/annonces/{annonce}',[AnnonceApiController::class, 'show']);
Route::get('/categories',        [AnnonceApiController::class, 'categories']);

// ── ROUTES PROTÉGÉES (token Sanctum requis) ───────────────────────
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::post('/auth/logout', [AuthApiController::class, 'logout']);
    Route::get('/auth/user',    [AuthApiController::class, 'user']);

    // Panier
    Route::get('/panier',              [CartApiController::class, 'index']);
    Route::post('/panier',             [CartApiController::class, 'ajouter']);
    Route::delete('/panier',           [CartApiController::class, 'vider']);
    Route::delete('/panier/{cartItem}',[CartApiController::class, 'supprimer']);

    // Commandes
    Route::get('/commandes',             [CommandeApiController::class, 'index']);
    Route::get('/commandes/{commande}',  [CommandeApiController::class, 'show']);
    Route::post('/commandes/passer',     [CommandeApiController::class, 'passer']);

    // ── FOURNISSEUR ──────────────────────────────────────────────────────
    Route::get('/mes-annonces',                          [FournisseurApiController::class, 'mesAnnonces']);
    Route::post('/annonces',                             [FournisseurApiController::class, 'creerAnnonce']);
    Route::put('/annonces/{annonce}',                    [FournisseurApiController::class, 'modifierAnnonce']);
    Route::delete('/mes-annonces/{annonce}',             [FournisseurApiController::class, 'supprimerAnnonce']);
    Route::post('/annonces/{annonce}/photos',            [FournisseurApiController::class, 'ajouterPhoto']);
    Route::get('/fournisseur/commandes',                 [FournisseurApiController::class, 'commandes']);
    Route::post('/commandes/items/{item}/accepter',      [FournisseurApiController::class, 'accepterItem']);
    Route::post('/commandes/items/{item}/refuser',       [FournisseurApiController::class, 'refuserItem']);

    // ── ADMIN ────────────────────────────────────────────────────────────
    Route::prefix('admin')->group(function () {
        Route::get('/dashboard',                          [AdminApiController::class, 'dashboard']);
        Route::get('/utilisateurs',                       [AdminApiController::class, 'utilisateurs']);
        Route::post('/utilisateurs/{user}/suspendre',     [AdminApiController::class, 'suspendreUtilisateur']);
        Route::post('/utilisateurs/{user}/activer',       [AdminApiController::class, 'activerUtilisateur']);
        Route::delete('/utilisateurs/{user}',             [AdminApiController::class, 'supprimerUtilisateur']);
        Route::get('/annonces',                           [AdminApiController::class, 'annonces']);
        Route::delete('/annonces/{annonce}',              [AdminApiController::class, 'supprimerAnnonce']);
        Route::get('/signalements',                       [AdminApiController::class, 'signalements']);
        Route::post('/signalements/{signalement}/traiter',[AdminApiController::class, 'traiterSignalement']);
        Route::get('/commandes',                          [AdminApiController::class, 'commandes']);
    });
});
