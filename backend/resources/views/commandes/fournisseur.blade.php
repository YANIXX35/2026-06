@extends('layouts.app')
@section('title', 'Commandes reçues')

@push('styles')
<style>
.cmd-wrap { max-width:900px; margin:0 auto; padding:40px 16px; }
.page-title { font-size:1.4rem; font-weight:800; color:#1a1a2e; margin-bottom:24px; }
.item-card { background:#fff; border-radius:16px; box-shadow:0 1px 6px rgba(0,0,0,.07); padding:20px; margin-bottom:14px; display:flex; gap:14px; align-items:center; }
.item-img { width:64px; height:64px; object-fit:cover; border-radius:10px; flex-shrink:0; }
.item-img-ph { width:64px; height:64px; border-radius:10px; background:#f3f4f6; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.item-info { flex:1; min-width:0; }
.item-title { font-weight:700; font-size:.9rem; color:#1a1a2e; margin-bottom:3px; }
.item-meta { font-size:.76rem; color:#9ca3af; line-height:1.8; }
.item-price { font-weight:800; font-size:.95rem; color:#16a34a; white-space:nowrap; }
.pill { display:inline-flex; align-items:center; gap:5px; padding:4px 12px; border-radius:50px; font-size:.72rem; font-weight:700; }
.pill-orange { background:#fef9c3; color:#b45309; }
.pill-green  { background:#f0fdf4; color:#16a34a; }
.pill-red    { background:#fef2f2; color:#ef4444; }
.pill-blue   { background:#eff6ff; color:#1d4ed8; }
.pill-dot    { width:6px; height:6px; border-radius:50%; background:currentColor; }
.acheteur-box { font-size:.78rem; color:#555; background:#f9fafb; border-radius:10px; padding:10px 14px; margin-top:8px; }
.btn-act { border:none; padding:7px 18px; border-radius:50px; font-size:.78rem; font-weight:700; cursor:pointer; transition:.2s; }
.btn-accept { background:#f0fdf4; color:#16a34a; }
.btn-accept:hover { background:#16a34a; color:#fff; }
.btn-refuse { background:#fef2f2; color:#ef4444; }
.btn-refuse:hover { background:#ef4444; color:#fff; }
</style>
@endpush

@section('content')
<div class="cmd-wrap">
    <div class="d-flex align-items-center gap-3 mb-4 flex-wrap">
        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary rounded-pill btn-sm">
            <i class="fas fa-arrow-left me-1"></i>Retour
        </a>
        <div class="page-title mb-0">📦 Commandes reçues</div>
    </div>

    @if(session('success'))
        <div class="alert alert-success rounded-3 mb-4">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger rounded-3 mb-4">{{ session('error') }}</div>
    @endif

    {{-- Filtres rapides --}}
    @php
        $enAttente = $items->getCollection()->where('statut','en_attente')->count();
        $acceptes  = $items->getCollection()->where('statut','accepté')->count();
        $refuses   = $items->getCollection()->where('statut','refusé')->count();
    @endphp
    <div class="d-flex gap-2 mb-4 flex-wrap">
        <span class="pill pill-orange"><span class="pill-dot"></span>{{ $enAttente }} en attente</span>
        <span class="pill pill-green"><span class="pill-dot"></span>{{ $acceptes }} accepté(s)</span>
        <span class="pill pill-red"><span class="pill-dot"></span>{{ $refuses }} refusé(s)</span>
    </div>

    @forelse($items as $item)
    @php
        $pillMap = ['en_attente'=>'pill-orange','accepté'=>'pill-green','refusé'=>'pill-red'];
        $labelMap = ['en_attente'=>'En attente','accepté'=>'Accepté','refusé'=>'Refusé'];
        $acheteur = $item->commande->acheteur ?? null;
    @endphp
    <div class="item-card">
        @if($item->annonce && $item->annonce->photoPrincipale)
            <img src="{{ $item->annonce->photoPrincipale->url }}" class="item-img"
                 alt="{{ $item->annonce->titre }}" onerror="this.src='{{ asset('img/no-image.jpg') }}'">
        @else
            <div class="item-img-ph"><i class="fas fa-box" style="color:#9ca3af;"></i></div>
        @endif

        <div class="item-info">
            <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                <span class="item-title">{{ $item->annonce->titre ?? 'Article supprimé' }}</span>
                <span class="pill {{ $pillMap[$item->statut] ?? 'pill-blue' }}">
                    <span class="pill-dot"></span>{{ $labelMap[$item->statut] ?? $item->statut }}
                </span>
            </div>
            <div class="item-meta">
                <i class="fas fa-layer-group me-1"></i>Quantité : <strong>{{ $item->quantite }} {{ $item->annonce->unite ?? '' }}</strong>
                &nbsp;·&nbsp;
                <i class="fas fa-calendar me-1"></i>{{ $item->created_at->format('d/m/Y à H:i') }}
                &nbsp;·&nbsp;
                Commande #{{ $item->commande_id }}
            </div>
            @if($acheteur)
            <div class="acheteur-box">
                <i class="fas fa-user me-1"></i>
                <strong>{{ $acheteur->prenom }} {{ $acheteur->nom }}</strong>
                @if($acheteur->telephone)
                    &nbsp;·&nbsp; <i class="fas fa-phone me-1"></i>{{ $acheteur->telephone }}
                @endif
                @if($item->commande->adresse_livraison)
                    &nbsp;·&nbsp; <i class="fas fa-map-marker-alt me-1"></i>{{ $item->commande->adresse_livraison }}
                @endif
                @if($item->commande->message)
                    <div class="mt-1 text-muted"><i class="fas fa-comment me-1"></i>{{ $item->commande->message }}</div>
                @endif
            </div>
            @endif
        </div>

        <div class="text-end d-flex flex-column gap-2 align-items-end">
            <div class="item-price">
                @if($item->prix_unitaire == 0) Gratuit
                @else {{ number_format($item->sousTotal(), 0, ',', ' ') }} F
                @endif
            </div>

            @if($item->statut === 'en_attente')
            <div class="d-flex gap-2">
                <form action="{{ route('commandes.items.accepter', $item) }}" method="POST">
                    @csrf
                    <button class="btn-act btn-accept" type="submit">
                        <i class="fas fa-check me-1"></i>Accepter
                    </button>
                </form>
                <form action="{{ route('commandes.items.refuser', $item) }}" method="POST">
                    @csrf
                    <button class="btn-act btn-refuse" type="submit" onclick="return confirm('Refuser cet article ?')">
                        <i class="fas fa-times me-1"></i>Refuser
                    </button>
                </form>
            </div>
            @endif

            @if($acheteur)
            <a href="{{ route('messages.ouvrir', $acheteur->id) }}"
               class="btn btn-outline-secondary rounded-pill btn-sm" style="font-size:.73rem;">
                <i class="fas fa-comment me-1"></i>Contacter
            </a>
            @endif
        </div>
    </div>
    @empty
    <div class="text-center py-5 text-muted">
        <div style="font-size:3rem;margin-bottom:12px;">📦</div>
        <p>Aucune commande reçue pour l'instant.</p>
    </div>
    @endforelse

    @if($items->hasPages())
    <div class="d-flex justify-content-center mt-4">{{ $items->links() }}</div>
    @endif
</div>
@endsection
