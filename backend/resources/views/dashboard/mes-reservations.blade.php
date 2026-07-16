@extends('layouts.app')
@section('title', 'Mes réservations')
@section('content')
<div class="container py-5">
    <h4 class="fw-bold mb-4"><i class="fas fa-handshake me-2 text-warning"></i>Mes réservations</h4>

    @if(session('success'))
        <div class="alert alert-success rounded-3">{{ session('success') }}</div>
    @endif

    @forelse($reservations as $reservation)
    <div class="card border-0 shadow-sm rounded-3 mb-3 overflow-hidden">
        {{-- Bande colorée selon le statut --}}
        @php
            $barColors = [
                'en_attente' => '#f59e0b',
                'acceptée'   => '#16a34a',
                'refusée'    => '#ef4444',
                'annulée'    => '#9ca3af',
                'complétée'  => '#3b82f6',
            ];
            $barColor = $barColors[$reservation->statut] ?? '#9ca3af';
        @endphp
        <div style="height:4px;background:{{ $barColor }};"></div>

        <div class="card-body p-4">
            <div class="row align-items-start g-3">
                {{-- Photo --}}
                <div class="col-auto">
                    @if($reservation->annonce && $reservation->annonce->photoPrincipale)
                        <img src="{{ $reservation->annonce->photoPrincipale->url }}" class="rounded" style="width:72px;height:72px;object-fit:cover;" onerror="this.src='{{ asset('img/no-image.jpg') }}'">
                    @else
                        <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width:72px;height:72px;">
                            <i class="fas fa-box text-muted fa-xl"></i>
                        </div>
                    @endif
                </div>

                {{-- Infos principales --}}
                <div class="col">
                    <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                        <h6 class="fw-bold mb-0">{{ $reservation->annonce->titre ?? 'Annonce supprimée' }}</h6>
                        @php
                            $badgeMap = [
                                'en_attente' => ['bg' => 'warning',   'text' => 'En attente',  'icon' => 'fa-clock'],
                                'acceptée'   => ['bg' => 'success',   'text' => 'Acceptée ✅', 'icon' => 'fa-check-circle'],
                                'refusée'    => ['bg' => 'danger',    'text' => 'Refusée ❌',  'icon' => 'fa-times-circle'],
                                'annulée'    => ['bg' => 'secondary', 'text' => 'Annulée',     'icon' => 'fa-ban'],
                                'complétée'  => ['bg' => 'primary',   'text' => 'Complétée',   'icon' => 'fa-check-double'],
                            ];
                            $bm = $badgeMap[$reservation->statut] ?? ['bg' => 'secondary', 'text' => $reservation->statut, 'icon' => 'fa-circle'];
                        @endphp
                        <span class="badge bg-{{ $bm['bg'] }} rounded-pill fs-6" style="font-size:.78rem!important;">
                            <i class="fas {{ $bm['icon'] }} me-1" style="font-size:.7rem;"></i>{{ $bm['text'] }}
                        </span>
                    </div>

                    <div class="text-muted small mb-2">
                        <i class="fas fa-user me-1"></i>
                        @if(auth()->user()->isFournisseur())
                            Demandeur : <strong>{{ $reservation->acheteur->prenom ?? '—' }} {{ $reservation->acheteur->nom ?? '' }}</strong>
                        @else
                            Fournisseur : <strong>{{ $reservation->annonce->user->prenom ?? '—' }} {{ $reservation->annonce->user->nom ?? '' }}</strong>
                        @endif
                        <span class="mx-2">•</span>
                        <i class="fas fa-cubes me-1"></i>{{ $reservation->quantite_demandee }} {{ $reservation->annonce->unite ?? 'unités' }}
                        <span class="mx-2">•</span>
                        <i class="fas fa-calendar me-1"></i>{{ $reservation->created_at->format('d/m/Y') }}
                    </div>

                    @if($reservation->message)
                        <div class="text-muted small fst-italic mb-2">"{{ Str::limit($reservation->message, 100) }}"</div>
                    @endif

                    {{-- Bandeau de réponse visible uniquement pour l'acheteur --}}
                    @if(!auth()->user()->isFournisseur())
                        @if($reservation->statut === 'acceptée')
                        <div class="rounded-2 px-3 py-2 mt-1" style="background:#f0fdf4;border:1px solid #bbf7d0;">
                            <span style="font-size:.82rem;color:#166534;font-weight:600;">
                                <i class="fas fa-check-circle me-1 text-success"></i>
                                Le fournisseur a accepté votre demande. Contactez-le pour organiser la collecte.
                            </span>
                        </div>
                        @elseif($reservation->statut === 'refusée')
                        <div class="rounded-2 px-3 py-2 mt-1" style="background:#fef2f2;border:1px solid #fecaca;">
                            <span style="font-size:.82rem;color:#991b1b;font-weight:600;">
                                <i class="fas fa-times-circle me-1 text-danger"></i>
                                Le fournisseur n'a pas pu accepter votre demande. L'annonce est de nouveau disponible.
                            </span>
                        </div>
                        @elseif($reservation->statut === 'complétée')
                        <div class="rounded-2 px-3 py-2 mt-1" style="background:#eff6ff;border:1px solid #bfdbfe;">
                            <span style="font-size:.82rem;color:#1e40af;font-weight:600;">
                                <i class="fas fa-check-double me-1" style="color:#3b82f6;"></i>
                                Échange complété avec succès !
                            </span>
                        </div>
                        @endif
                    @endif
                </div>

                {{-- Actions --}}
                <div class="col-12 col-md-auto text-md-end d-flex flex-wrap gap-2 align-items-center justify-content-end">
                    @if(auth()->user()->isFournisseur() && $reservation->statut === 'en_attente')
                        <form action="{{ route('reservations.accepter', $reservation) }}" method="POST">
                            @csrf
                            <button class="btn btn-sm btn-success rounded-pill px-3">
                                <i class="fas fa-check me-1"></i>Accepter
                            </button>
                        </form>
                        <form action="{{ route('reservations.refuser', $reservation) }}" method="POST">
                            @csrf
                            <button class="btn btn-sm btn-danger rounded-pill px-3">
                                <i class="fas fa-times me-1"></i>Refuser
                            </button>
                        </form>
                    @elseif(auth()->user()->isFournisseur() && $reservation->statut === 'acceptée')
                        <form action="{{ route('reservations.completer', $reservation) }}" method="POST">
                            @csrf
                            <button class="btn btn-sm btn-primary rounded-pill px-3">
                                <i class="fas fa-check-double me-1"></i>Marquer complétée
                            </button>
                        </form>
                        <a href="{{ route('messages.ouvrir', $reservation->user_id) }}?annonce_id={{ $reservation->annonce_id }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                            <i class="fas fa-comment me-1"></i>Contacter
                        </a>
                    @elseif(!auth()->user()->isFournisseur() && $reservation->statut === 'en_attente')
                        <form action="{{ route('reservations.annuler', $reservation) }}" method="POST">
                            @csrf
                            <button class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                                <i class="fas fa-ban me-1"></i>Annuler
                            </button>
                        </form>
                    @elseif(!auth()->user()->isFournisseur() && $reservation->statut === 'acceptée')
                        <a href="{{ route('messages.ouvrir', $reservation->annonce->user_id) }}?annonce_id={{ $reservation->annonce_id }}" class="btn btn-sm btn-success rounded-pill px-3">
                            <i class="fas fa-comment me-1"></i>Contacter le fournisseur
                        </a>
                    @elseif($reservation->statut === 'complétée' && !$reservation->avis)
                        <a href="{{ route('annonces.show', $reservation->annonce) }}#avis" class="btn btn-sm btn-warning rounded-pill px-3">
                            <i class="fas fa-star me-1"></i>Laisser un avis
                        </a>
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
