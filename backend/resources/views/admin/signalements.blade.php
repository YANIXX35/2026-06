@extends('layouts.app')
@section('title', 'Gestion des signalements')
@section('content')
<div class="container-fluid bg-dark text-white py-3 mb-4">
    <div class="container">
        <h5 class="mb-0"><i class="fas fa-flag me-2 text-warning"></i>Gestion des signalements</h5>
    </div>
</div>
<div class="container pb-5">
    @if(session('success'))
        <div class="alert alert-success rounded-3">{{ session('success') }}</div>
    @endif

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-3 text-center p-3">
                <h4 class="fw-bold text-warning">{{ $signalements->where('statut','en_attente')->count() }}</h4>
                <p class="text-muted small mb-0">En attente</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-3 text-center p-3">
                <h4 class="fw-bold text-success">{{ $signalements->where('statut','traité')->count() }}</h4>
                <p class="text-muted small mb-0">Traités</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-3 text-center p-3">
                <h4 class="fw-bold text-secondary">{{ $signalements->where('statut','rejeté')->count() }}</h4>
                <p class="text-muted small mb-0">Rejetés</p>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white p-4 d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-semibold">Tous les signalements</h6>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-outline-secondary rounded-pill">
                <i class="fas fa-arrow-left me-1"></i>Retour
            </a>
        </div>
        <div class="card-body p-0">
            @forelse($signalements as $sig)
            <div class="p-4 border-bottom">
                <div class="row align-items-start">
                    <div class="col">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="fw-semibold">{{ $sig->raison }}</span>
                            @php $bc = ['en_attente'=>'warning','traité'=>'success','rejeté'=>'secondary']; @endphp
                            <span class="badge bg-{{ $bc[$sig->statut] ?? 'secondary' }} rounded-pill">{{ $sig->statut }}</span>
                        </div>
                        @if($sig->description)
                            <p class="text-muted small mb-1">{{ $sig->description }}</p>
                        @endif
                        <div class="text-muted small">
                            <i class="fas fa-user me-1"></i>Par : {{ $sig->auteur->prenom ?? '—' }} {{ $sig->auteur->nom ?? '' }}
                            @if($sig->annonce)
                                <span class="mx-2">•</span>
                                <i class="fas fa-bullhorn me-1"></i>Annonce :
                                <a href="{{ route('annonces.show', $sig->annonce) }}" class="text-primary">{{ Str::limit($sig->annonce->titre, 40) }}</a>
                            @endif
                            <span class="mx-2">•</span>
                            <i class="fas fa-clock me-1"></i>{{ $sig->created_at->diffForHumans() }}
                        </div>
                    </div>
                    <div class="col-auto">
                        @if($sig->statut === 'en_attente')
                            <form action="{{ route('admin.traiter-signalement', $sig) }}" method="POST" class="d-inline">
                                @csrf
                                <button class="btn btn-sm btn-outline-success rounded-pill px-3">
                                    <i class="fas fa-check me-1"></i>Traité
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-5 text-muted">
                <i class="fas fa-flag fa-3x mb-3 opacity-25"></i>
                <p>Aucun signalement</p>
            </div>
            @endforelse
        </div>
        @if($signalements->hasPages())
        <div class="card-footer d-flex justify-content-between align-items-center py-2">
            <small class="text-muted">Affichage {{ $signalements->firstItem() }}–{{ $signalements->lastItem() }} sur {{ $signalements->total() }} signalements</small>
            {{ $signalements->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
