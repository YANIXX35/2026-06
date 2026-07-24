@extends('layouts.app')

@section('title', 'Négocier le prix — ' . $annonce->titre)

@push('styles')
<style>
    .glass-card {
        background: rgba(255, 255, 255, 0.45);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.25);
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.08);
    }
    .negocier-hero-img {
        height: 250px;
        object-fit: cover;
        width: 100%;
        border-radius: 16px;
    }
</style>
@endpush

@section('content')
<div class="container-fluid bg-light py-3 border-bottom mb-5">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="/" class="text-primary">Accueil</a></li>
                <li class="breadcrumb-item"><a href="{{ route('annonces.index') }}" class="text-primary">Annonces</a></li>
                <li class="breadcrumb-item"><a href="{{ route('annonces.show', $annonce) }}" class="text-primary">{{ Str::limit($annonce->titre, 30) }}</a></li>
                <li class="breadcrumb-item active">Négocier</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container mb-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card glass-card border-0 rounded-4 overflow-hidden">
                <div class="row g-0">
                    <!-- Section Détails Produit -->
                    <div class="col-md-5 bg-success bg-opacity-10 p-4 d-flex flex-column justify-content-between border-end border-white">
                        <div>
                            @if($annonce->photoPrincipale)
                                <img src="{{ $annonce->photoPrincipale->url }}" class="negocier-hero-img shadow-sm mb-4" alt="{{ $annonce->titre }}">
                            @else
                                <div class="bg-white rounded-3 d-flex align-items-center justify-content-center shadow-sm mb-4" style="height: 250px;">
                                    <i class="fas fa-box text-success fa-3x"></i>
                                </div>
                            @endif
                            
                            <h4 class="fw-bold text-success mb-2">{{ $annonce->titre }}</h4>
                            <p class="text-secondary small mb-4">{{ Str::limit($annonce->description, 120) }}</p>
                            
                            <div class="d-flex flex-column gap-2 mb-4">
                                <div class="d-flex justify-content-between py-2 border-bottom border-white">
                                    <span class="text-muted"><i class="fas fa-store me-2 text-success"></i>Vendeur</span>
                                    <span class="fw-bold text-dark">{{ $annonce->user->nom_structure ?? $annonce->user->prenom.' '.$annonce->user->nom }}</span>
                                </div>
                                <div class="d-flex justify-content-between py-2 border-bottom border-white">
                                    <span class="text-muted"><i class="fas fa-tag me-2 text-success"></i>Prix actuel</span>
                                    <span class="fw-bold text-dark fs-5">{{ number_format($annonce->prix, 0, ',', ' ') }} FCFA</span>
                                </div>
                                <div class="d-flex justify-content-between py-2 border-bottom border-white">
                                    <span class="text-muted"><i class="fas fa-boxes me-2 text-success"></i>Quantité dispo</span>
                                    <span class="fw-semibold text-dark">{{ $annonce->quantite }} {{ $annonce->unite }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-success bg-white border-0 shadow-sm rounded-3 py-3 d-flex gap-3 mb-0" style="font-size: .8rem;">
                            <i class="fas fa-shield-alt text-success fs-4"></i>
                            <div>
                                <span class="fw-bold text-success d-block mb-1">Garantie AntiGaspiCI</span>
                                La négociation en ligne sur notre plateforme garantit la validité du tarif négocié lors du paiement.
                            </div>
                        </div>
                    </div>

                    <!-- Section Formulaire -->
                    <div class="col-md-7 p-4 p-lg-5 bg-white">
                        <div class="d-flex align-items-center gap-3 mb-4">
                            <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                <i class="fas fa-handshake fs-5"></i>
                            </div>
                            <div>
                                <h4 class="fw-bold mb-1">Proposer un prix négocié</h4>
                                <p class="text-muted small mb-0">Remplissez le formulaire ci-dessous pour soumettre votre offre au fournisseur.</p>
                            </div>
                        </div>

                        @if(session('error'))
                            <div class="alert alert-danger rounded-3 mb-4"><i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}</div>
                        @endif

                        <form action="{{ route('annonces.negocier', $annonce) }}" method="POST">
                            @csrf
                            
                            <div class="mb-4">
                                <label class="form-label fw-bold text-dark">Votre prix proposé (FCFA)</label>
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text bg-light text-success fw-bold">💰</span>
                                    <input type="number" name="prix_propose" id="prix_propose" class="form-control fw-bold text-success" 
                                           placeholder="Ex: {{ floor($annonce->prix * 0.8) }}" min="1" max="{{ $annonce->prix - 1 }}" required>
                                    <span class="input-group-text bg-light fw-bold text-secondary">FCFA</span>
                                </div>
                                <div class="form-text text-success mt-2">
                                    <i class="fas fa-info-circle me-1"></i> Conseil : Une réduction de 10% à 30% augmente vos chances d'acceptation.
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold text-dark">Pour quelle quantité ? (En {{ $annonce->unite }})</label>
                                <div class="input-group">
                                    <input type="number" name="quantite" class="form-control form-control-lg" step="0.01" min="0.01" max="{{ $annonce->quantite }}" value="{{ min(1, $annonce->quantite) }}" required>
                                    <span class="input-group-text bg-light text-secondary">{{ $annonce->unite }}</span>
                                </div>
                                <div class="form-text mt-2">Quantité disponible : {{ $annonce->quantite }} {{ $annonce->unite }}</div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold text-dark">Votre message au fournisseur (Optionnel)</label>
                                <textarea name="message" class="form-control" rows="4" placeholder="Bonjour, je vous propose ce tarif car..."></textarea>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-success btn-lg rounded-pill fw-bold py-3 shadow">
                                    <i class="fas fa-paper-plane me-2"></i>Soumettre ma proposition
                                </button>
                                <a href="{{ route('annonces.show', $annonce) }}" class="btn btn-light btn-lg rounded-pill fw-semibold py-3 text-secondary">
                                    Annuler
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
