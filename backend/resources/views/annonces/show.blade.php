@extends('layouts.app')
@section('title', $annonce->titre)

@section('content')
<div class="container-fluid bg-light py-3 border-bottom">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="/" class="text-primary">Accueil</a></li>
                <li class="breadcrumb-item"><a href="{{ route('annonces.index') }}" class="text-primary">Annonces</a></li>
                <li class="breadcrumb-item active">{{ Str::limit($annonce->titre, 40) }}</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container py-5">
    <div class="row g-5">
        <!-- Photos + Détails -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-body p-0">
                    @if($annonce->photos->count())
                        <div class="owl-carousel" id="photoCarousel">
                            @foreach($annonce->photos as $photo)
                                <img src="{{ $photo->url }}" class="img-fluid w-100 rounded-top" style="max-height:400px;object-fit:cover;" alt="{{ $annonce->titre }}" onerror="this.style.display='none'">
                            @endforeach
                        </div>
                    @else
                        <div class="bg-light d-flex align-items-center justify-content-center rounded-top" style="height:300px;">
                            <i class="fas fa-image fa-5x text-muted"></i>
                        </div>
                    @endif
                    <div class="p-4">
                        @php
                            $badges = ['vente'=>['primary','💰 Vente'],'don'=>['success','🎁 Don gratuit'],'alimentation_animale'=>['warning','🐄 Alimentation animale'],'transformation'=>['info','🏭 Transformation']];
                            [$color, $label] = $badges[$annonce->type_offre] ?? ['secondary',$annonce->type_offre];
                        @endphp
                        <div class="d-flex flex-wrap gap-2 mb-3">
                            <span class="badge bg-{{ $color }} rounded-pill px-3">{{ $label }}</span>
                            <span class="badge bg-light text-dark border rounded-pill px-3">{{ $annonce->categorie->icone ?? '' }} {{ $annonce->categorie->nom }}</span>
                            <span class="badge bg-light text-dark border rounded-pill px-3">
                                <i class="fas fa-eye me-1 text-primary"></i>{{ $annonce->vues }} vues
                            </span>
                        </div>

                        <h2 class="fw-bold mb-3">{{ $annonce->titre }}</h2>

                        <div class="row g-3 mb-4">
                            <div class="col-sm-4">
                                <div class="bg-light rounded p-3 text-center">
                                    <div class="text-primary fw-bold fs-4">
                                        @if($annonce->type_offre === 'don') GRATUIT
                                        @else {{ number_format($annonce->prix, 0, ',', ' ') }} FCFA
                                        @endif
                                    </div>
                                    <small class="text-muted">Prix</small>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="bg-light rounded p-3 text-center">
                                    <div class="text-primary fw-bold fs-4">{{ $annonce->quantite }} {{ $annonce->unite }}</div>
                                    <small class="text-muted">Quantité disponible</small>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="bg-light rounded p-3 text-center">
                                    @if($annonce->date_expiration)
                                        <div class="fw-bold fs-6 {{ $annonce->estExpire() ? 'text-danger' : 'text-success' }}">
                                            {{ $annonce->date_expiration->format('d/m/Y H:i') }}
                                        </div>
                                    @else
                                        <div class="text-muted fw-bold">—</div>
                                    @endif
                                    <small class="text-muted">Expire le</small>
                                </div>
                            </div>
                        </div>

                        <h5 class="fw-semibold mb-2">Description</h5>
                        <p class="text-muted">{{ $annonce->description }}</p>

                        <hr>
                        <h5 class="fw-semibold mb-3"><i class="fas fa-map-marker-alt me-2 text-primary"></i>Lieu de collecte</h5>
                        <p class="text-muted">{{ $annonce->adresse_collecte }}</p>
                    </div>
                </div>
            </div>

            <!-- Annonces liées -->
            @if($annoncesLiees->count())
            <h5 class="fw-semibold mb-3">Annonces similaires</h5>
            <div class="row g-3">
                @foreach($annoncesLiees as $liee)
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm rounded-3 h-100">
                        <div class="card-body p-3">
                            <h6 class="fw-semibold">
                                <a href="{{ route('annonces.show', $liee) }}" class="text-dark text-decoration-none">{{ Str::limit($liee->titre, 50) }}</a>
                            </h6>
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <span class="text-muted small"><i class="fas fa-map-marker-alt me-1 text-primary"></i>{{ Str::limit($liee->adresse_collecte, 30) }}</span>
                                <span class="fw-bold text-primary small">
                                    @if($liee->type_offre==='don') GRATUIT @else {{ number_format($liee->prix,0,',',' ') }} FCFA @endif
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>

        <!-- Sidebar: Réservation + Fournisseur -->
        <div class="col-lg-4">
            <!-- Réservation -->
            @if($annonce->statut === 'disponible' && !$annonce->estExpire())
                @auth
                    @if(Auth::id() !== $annonce->user_id)

                    {{-- Flash panier --}}
                    @if(session('cart_success'))
                    <div class="alert alert-success rounded-3 mb-3 d-flex align-items-center gap-2 py-2 px-3">
                        <i class="fas fa-check-circle"></i>
                        <span class="flex-grow-1" style="font-size:.85rem;">{{ session('cart_success') }}</span>
                        <a href="{{ route('cart.index') }}" class="btn btn-sm btn-success rounded-pill px-3">Voir panier</a>
                    </div>
                    @endif

                    {{-- Ajouter au panier --}}
                    <div class="card border-0 shadow-sm rounded-3 mb-3">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <i class="fas fa-shopping-cart text-warning"></i>
                                <span class="fw-semibold" style="font-size:.9rem;">Ajouter au panier</span>
                            </div>
                            <form action="{{ route('cart.ajouter', $annonce) }}" method="POST" class="d-flex gap-2 align-items-start">
                                @csrf
                                <div class="flex-grow-1">
                                    <input type="number" name="quantite" class="form-control form-control-sm" step="0.01" min="0.01" max="{{ $annonce->quantite }}" value="{{ min(1, $annonce->quantite) }}" required>
                                    <small class="text-muted">Dispo : {{ $annonce->quantite }} {{ $annonce->unite }}</small>
                                </div>
                                <button type="submit" class="btn btn-warning rounded-pill fw-semibold px-3" style="white-space:nowrap;">
                                    <i class="fas fa-cart-plus me-1"></i>Ajouter
                                </button>
                            </form>
                        </div>
                    </div>

                    {{-- Réservation directe --}}
                    <div class="card border-0 shadow-sm rounded-3 mb-4">
                        <div class="card-header bg-primary text-white rounded-top-3">
                            <h6 class="mb-0"><i class="fas fa-hand-holding-heart me-2"></i>
                                {{ $annonce->type_offre === 'don' ? 'Demander ce don' : 'Réserver directement' }}
                            </h6>
                        </div>
                        <div class="card-body p-4">
                            <form action="{{ route('reservations.store', $annonce) }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Quantité souhaitée ({{ $annonce->unite }})</label>
                                    <input type="number" name="quantite_demandee" class="form-control" step="0.01" min="0.01" max="{{ $annonce->quantite }}" value="{{ $annonce->quantite }}" required>
                                    <small class="text-muted">Disponible : {{ $annonce->quantite }} {{ $annonce->unite }}</small>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Date de collecte souhaitée</label>
                                    <input type="datetime-local" name="date_collecte_souhaitee" class="form-control">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Message au fournisseur</label>
                                    <textarea name="message" class="form-control" rows="3" placeholder="Précisez vos besoins..."></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary w-100 rounded-pill py-3 fw-semibold">
                                    <i class="fas fa-paper-plane me-2"></i>Envoyer la demande
                                </button>
                            </form>
                        </div>
                    </div>
                    @endif
                @else
                    <div class="card border-0 shadow-sm rounded-3 mb-4">
                        <div class="card-body p-4 text-center">
                            <i class="fas fa-lock fa-3x text-primary mb-3"></i>
                            <h6>Connectez-vous pour réserver</h6>
                            <a href="{{ route('connexion') }}" class="btn btn-primary rounded-pill px-5 mt-2">Se connecter</a>
                            <p class="text-muted small mt-2">Pas encore de compte ? <a href="{{ route('inscription') }}">S'inscrire</a></p>
                        </div>
                    </div>
                @endauth
            @else
                <div class="alert alert-warning rounded-3 mb-4">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    @if($annonce->statut === 'reservé') Ce produit est déjà réservé.
                    @elseif($annonce->statut === 'expiré' || $annonce->estExpire()) Cette annonce a expiré.
                    @else Ce produit n'est plus disponible.
                    @endif
                </div>
            @endif

            <!-- Profil Fournisseur -->
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-body p-4">
                    <h6 class="fw-semibold mb-3"><i class="fas fa-store me-2 text-primary"></i>Le fournisseur</h6>
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-3" style="width:50px;height:50px;">
                            <span class="text-white fw-bold">{{ strtoupper(substr($annonce->user->prenom, 0, 1)) }}</span>
                        </div>
                        <div>
                            <div class="fw-semibold">{{ $annonce->user->nom_structure ?? $annonce->user->prenom.' '.$annonce->user->nom }}</div>
                            <small class="text-muted">{{ ucfirst($annonce->user->type_structure ?? 'Particulier') }}</small>
                        </div>
                    </div>
                    @if($annonce->user->note_moyenne > 0)
                    <div class="mb-3">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="fas fa-star {{ $i <= round($annonce->user->note_moyenne) ? 'text-warning' : 'text-muted' }}"></i>
                        @endfor
                        <span class="text-muted small ms-1">({{ number_format($annonce->user->note_moyenne, 1) }}/5)</span>
                    </div>
                    @endif
                    @auth
                        @if(Auth::id() !== $annonce->user_id)
                        <a href="{{ route('messages.ouvrir', $annonce->user) }}" class="btn btn-outline-primary w-100 rounded-pill">
                            <i class="fas fa-comments me-2"></i>Envoyer un message
                        </a>
                        @endif
                    @endauth
                </div>
            </div>

            <!-- Partager -->
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-4">
                    <h6 class="fw-semibold mb-3"><i class="fas fa-share-alt me-2 text-primary"></i>Partager</h6>
                    <div class="d-flex gap-2">
                        <a href="#" class="btn btn-primary btn-sm rounded-circle"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="btn btn-info btn-sm rounded-circle text-white"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="btn btn-success btn-sm rounded-circle"><i class="fab fa-whatsapp"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
