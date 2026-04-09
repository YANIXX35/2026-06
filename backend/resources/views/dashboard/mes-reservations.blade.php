@extends('layouts.app')
@section('title', 'Mes réservations')
@section('content')
<div class="container py-5">
    <h4 class="fw-bold mb-4"><i class="fas fa-handshake me-2 text-warning"></i>Mes réservations</h4>

    @if(session('success'))
        <div class="alert alert-success rounded-3">{{ session('success') }}</div>
    @endif

    @forelse($reservations as $reservation)
    <div class="card border-0 shadow-sm rounded-3 mb-3">
        <div class="card-body p-4">
            <div class="row align-items-center">
                <div class="col-auto">
                    @if($reservation->annonce && $reservation->annonce->photoPrincipale)
                        <img src="{{ asset('storage/'.$reservation->annonce->photoPrincipale->url) }}" class="rounded" style="width:64px;height:64px;object-fit:cover;" onerror="this.src='{{ asset('img/no-image.jpg') }}'">
                    @else
                        <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width:64px;height:64px;">
                            <i class="fas fa-box text-muted fa-xl"></i>
                        </div>
                    @endif
                </div>
                <div class="col">
                    <h6 class="fw-bold mb-1">{{ $reservation->annonce->titre ?? 'Annonce supprimée' }}</h6>
                    <div class="text-muted small mb-2">
                        <i class="fas fa-user me-1"></i>
                        @if(auth()->user()->isFournisseur())
                            Demandeur : {{ $reservation->acheteur->prenom }} {{ $reservation->acheteur->nom }}
                        @else
                            Fournisseur : {{ $reservation->annonce->user->prenom ?? '—' }} {{ $reservation->annonce->user->nom ?? '' }}
                        @endif
                        <span class="mx-2">•</span>
                        <i class="fas fa-cubes me-1"></i>{{ $reservation->quantite_demandee }} unités
                        <span class="mx-2">•</span>
                        <i class="fas fa-calendar me-1"></i>{{ $reservation->created_at->format('d/m/Y') }}
                    </div>
                    @if($reservation->message)
                        <div class="text-muted small fst-italic">"{{ Str::limit($reservation->message, 80) }}"</div>
                    @endif
                </div>
                <div class="col-auto text-end">
                    @php
                        $badges = ['en_attente'=>'warning','acceptée'=>'success','refusée'=>'danger','annulée'=>'secondary','complétée'=>'primary'];
                        $badge = $badges[$reservation->statut] ?? 'secondary';
                    @endphp
                    <span class="badge bg-{{ $badge }} rounded-pill mb-2 d-block">{{ $reservation->statut }}</span>

                    @if(auth()->user()->isFournisseur() && $reservation->statut === 'en_attente')
                        <div class="d-flex gap-1">
                            <form action="{{ route('reservations.accepter', $reservation) }}" method="POST">
                                @csrf
                                <button class="btn btn-sm btn-success rounded-pill px-2">Accepter</button>
                            </form>
                            <form action="{{ route('reservations.refuser', $reservation) }}" method="POST">
                                @csrf
                                <button class="btn btn-sm btn-danger rounded-pill px-2">Refuser</button>
                            </form>
                        </div>
                    @elseif(auth()->user()->isAcheteur() && $reservation->statut === 'en_attente')
                        <form action="{{ route('reservations.annuler', $reservation) }}" method="POST">
                            @csrf
                            <button class="btn btn-sm btn-outline-secondary rounded-pill px-2">Annuler</button>
                        </form>
                    @elseif(auth()->user()->isFournisseur() && $reservation->statut === 'acceptée')
                        <form action="{{ route('reservations.completer', $reservation) }}" method="POST">
                            @csrf
                            <button class="btn btn-sm btn-primary rounded-pill px-2">Marquer complétée</button>
                        </form>
                    @elseif($reservation->statut === 'complétée' && !$reservation->avis)
                        <a href="{{ route('annonces.show', $reservation->annonce) }}#avis" class="btn btn-sm btn-outline-warning rounded-pill px-2">Laisser un avis</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="text-center py-5">
        <i class="fas fa-handshake fa-4x text-muted mb-4 opacity-25"></i>
        <h5 class="text-muted">Aucune réservation</h5>
        <a href="{{ route('annonces.index') }}" class="btn btn-primary rounded-pill px-5 py-2 mt-2">
            <i class="fas fa-search me-2"></i>Parcourir les annonces
        </a>
    </div>
    @endforelse

    <div class="mt-4">{{ $reservations->links() }}</div>
</div>
@endsection
