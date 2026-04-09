@extends('layouts.app')
@section('title', 'Comment ça marche')
@section('content')
<div class="container py-5">
    <div class="text-center mb-5">
        <h4 class="text-primary border-bottom border-primary border-2 d-inline-block p-2">Fonctionnement</h4>
        <h1 class="display-5 mt-3">Comment ça marche ?</h1>
        <p class="text-muted lead">AntiGaspiCI connecte fournisseurs et bénéficiaires en 4 étapes simples</p>
    </div>
    <div class="row g-4 mb-5">
        @foreach([
            ['fas fa-user-plus','1. S\'inscrire','Créez votre compte gratuit en tant que fournisseur (restaurant, marché, artisan...) ou acheteur (particulier, association, éleveur...). La vérification est rapide.','primary'],
            ['fas fa-bullhorn','2. Publier','Les fournisseurs publient leurs surplus en temps réel : photo, quantité, prix, localisation. L\'annonce est visible immédiatement par tous les utilisateurs proches.','success'],
            ['fas fa-search-location','3. Trouver','Les acheteurs cherchent des produits par catégorie, localisation ou type d\'offre (vente, don, alimentation animale). La carte interactive facilite la recherche.','warning'],
            ['fas fa-handshake','4. Échanger','L\'acheteur envoie une demande de réservation. Le fournisseur accepte ou refuse. Ils se mettent en contact via la messagerie pour organiser la collecte.','info'],
        ] as $step)
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-3 h-100 text-center p-4">
                <div class="rounded-circle bg-{{ $step[3] }} d-flex align-items-center justify-content-center mx-auto mb-4" style="width:80px;height:80px;">
                    <i class="{{ $step[0] }} fa-2x text-white"></i>
                </div>
                <h5 class="fw-bold mb-3">{{ $step[1] }}</h5>
                <p class="text-muted">{{ $step[2] }}</p>
            </div>
        </div>
        @endforeach
    </div>
    <div class="text-center">
        <a href="{{ route('inscription') }}" class="btn btn-primary btn-lg rounded-pill px-5 me-3">
            <i class="fas fa-user-plus me-2"></i>Je m'inscris gratuitement
        </a>
        <a href="{{ route('annonces.index') }}" class="btn btn-outline-primary btn-lg rounded-pill px-5">
            <i class="fas fa-search me-2"></i>Voir les annonces
        </a>
    </div>
</div>
@endsection
