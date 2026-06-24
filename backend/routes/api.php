<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\AnnonceApiController;
use App\Http\Controllers\Api\CartApiController;
use App\Http\Controllers\Api\CommandeApiController;

// ── ROUTES PUBLIQUES ──────────────────────────────────────────────
Route::post('/auth/login',    [AuthApiController::class, 'login']);
Route::post('/auth/register', [AuthApiController::class, 'register']);

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
});
