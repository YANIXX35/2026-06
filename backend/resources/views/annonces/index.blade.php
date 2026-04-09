@extends('layouts.app')
@section('title', 'Toutes les annonces')

@section('content')
<div class="container-fluid bg-light py-3 border-bottom">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="/" class="text-primary">Accueil</a></li>
                <li class="breadcrumb-item active">Annonces</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container-fluid py-5">
    <div class="container">
        <div class="row g-4">

            <!-- Filtres sidebar -->
            <div class="col-lg-3">
                <div class="card border-0 shadow-sm rounded-3 sticky-top" style="top:10px;">
                    <div class="card-header bg-primary text-white rounded-top-3">
                        <h6 class="mb-0"><i class="fas fa-filter me-2"></i>Filtres</h6>
                    </div>
                    <div class="card-body p-4">
                        <form action="{{ route('annonces.index') }}" method="GET">

                            <div class="mb-3">
                                <label class="form-label fw-semibold small text-muted text-uppercase">Type d'offre</label>
                                @foreach(['vente' => '💰 Vente', 'don' => '🎁 Don gratuit', 'alimentation_animale' => '🐄 Alimentation animale', 'transformation' => '🏭 Transformation'] as $val => $label)
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="type_offre" value="{{ $val }}" id="t_{{ $val }}" {{ request('type_offre') === $val ? 'checked' : '' }}>
                                    <label class="form-check-label" for="t_{{ $val }}">{{ $label }}</label>
                                </div>
                                @endforeach
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="type_offre" value="" id="t_all" {{ !request('type_offre') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="t_all">Toutes</label>
                                </div>
                            </div>

                            <hr>
                            <div class="mb-3">
                                <label class="form-label fw-semibold small text-muted text-uppercase">Catégorie</label>
                                @foreach($categories as $cat)
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="categorie" value="{{ $cat->id }}" id="c_{{ $cat->id }}" {{ request('categorie') == $cat->id ? 'checked' : '' }}>
                                    <label class="form-check-label" for="c_{{ $cat->id }}">{{ $cat->icone }} {{ $cat->nom }}</label>
                                </div>
                                @endforeach
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="categorie" value="" id="c_all" {{ !request('categorie') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="c_all">Toutes</label>
                                </div>
                            </div>

                            <hr>
                            <div class="mb-3">
                                <label class="form-label fw-semibold small text-muted text-uppercase">Ville / Quartier</label>
                                <input type="text" name="ville" class="form-control form-control-sm" value="{{ request('ville') }}" placeholder="Ex: Cocody, Yopougon...">
                            </div>

                            <button type="submit" class="btn btn-primary w-100 rounded-pill">
                                <i class="fas fa-search me-2"></i>Appliquer
                            </button>
                            <a href="{{ route('annonces.index') }}" class="btn btn-outline-secondary w-100 rounded-pill mt-2">
                                <i class="fas fa-times me-2"></i>Réinitialiser
                            </a>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Liste annonces -->
            <div class="col-lg-9">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="mb-0">
                        <span class="badge bg-primary rounded-pill me-2">{{ $annonces->total() }}</span>
                        annonce(s) disponible(s)
                    </h5>
                    <select class="form-select form-select-sm w-auto" onchange="window.location='?tri='+this.value">
                        <option value="recent">Plus récentes</option>
                        <option value="prix_asc">Prix croissant</option>
                        <option value="prix_desc">Prix décroissant</option>
                    </select>
                </div>

                @forelse($annonces as $annonce)
                <div class="card border-0 shadow-sm rounded-3 mb-4 wow fadeInUp" data-wow-delay="0.1s">
                    <div class="row g-0">
                        <div class="col-md-3">
                            <div class="position-relative h-100">
                                @if($annonce->photoPrincipale)
                                    <img src="{{ Storage::url($annonce->photoPrincipale->url) }}" class="img-fluid h-100 rounded-start" style="object-fit:cover;width:100%;" alt="{{ $annonce->titre }}">
                                @else
                                    <div class="bg-light d-flex align-items-center justify-content-center h-100 rounded-start" style="min-height:150px;">
                                        <i class="fas fa-image fa-3x text-muted"></i>
                                    </div>
                                @endif
                                @php
                                    $badges = ['vente'=>['bg-primary','💰 Vente'], 'don'=>['bg-success','🎁 Don'], 'alimentation_animale'=>['bg-warning','🐄 Animale'], 'transformation'=>['bg-purple','🏭 Transfo']];
                                    [$bg, $label] = $badges[$annonce->type_offre] ?? ['bg-secondary', $annonce->type_offre];
                                @endphp
                                <span class="badge {{ $bg }} position-absolute top-0 start-0 m-2">{{ $label }}</span>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <span class="badge bg-light text-dark border mb-2">{{ $annonce->categorie->icone ?? '' }} {{ $annonce->categorie->nom }}</span>
                                        <h5 class="card-title mb-1">
                                            <a href="{{ route('annonces.show', $annonce) }}" class="text-dark text-decoration-none">{{ $annonce->titre }}</a>
                                        </h5>
                                        <p class="text-muted small mb-2">{{ Str::limit($annonce->description, 100) }}</p>
                                    </div>
                                    <div class="text-end ms-3">
                                        @if($annonce->type_offre === 'don')
                                            <span class="h5 text-success fw-bold">GRATUIT</span>
                                        @else
                                            <span class="h5 text-primary fw-bold">{{ number_format($annonce->prix, 0, ',', ' ') }} FCFA</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="d-flex flex-wrap gap-3 text-muted small mt-2">
                                    <span><i class="fas fa-weight me-1 text-primary"></i>{{ $annonce->quantite }} {{ $annonce->unite }}</span>
                                    <span><i class="fas fa-map-marker-alt me-1 text-primary"></i>{{ $annonce->adresse_collecte }}</span>
                                    <span><i class="fas fa-user me-1 text-primary"></i>{{ $annonce->user->nom_structure ?? $annonce->user->prenom.' '.$annonce->user->nom }}</span>
                                    <span><i class="fas fa-clock me-1 text-primary"></i>{{ $annonce->created_at->diffForHumans() }}</span>
                                    <span><i class="fas fa-eye me-1 text-primary"></i>{{ $annonce->vues }} vues</span>
                                </div>
                                <div class="mt-3">
                                    <a href="{{ route('annonces.show', $annonce) }}" class="btn btn-primary btn-sm rounded-pill px-4 me-2">
                                        <i class="fas fa-eye me-1"></i>Voir l'annonce
                                    </a>
                                    @auth
                                        @if(Auth::id() !== $annonce->user_id)
                                        <a href="{{ route('messages.ouvrir', $annonce->user) }}" class="btn btn-outline-primary btn-sm rounded-pill px-4">
                                            <i class="fas fa-comment me-1"></i>Contacter
                                        </a>
                                        @endif
                                    @endauth
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-5">
                    <i class="fas fa-search fa-4x text-muted mb-4"></i>
                    <h4 class="text-muted">Aucune annonce disponible</h4>
                    <p class="text-muted">Essayez d'autres filtres ou revenez plus tard.</p>
                    <a href="{{ route('annonces.index') }}" class="btn btn-primary rounded-pill px-5">Voir toutes les annonces</a>
                </div>
                @endforelse

                <div class="d-flex justify-content-center mt-4">
                    {{ $annonces->withQueryString()->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
