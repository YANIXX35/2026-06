@extends('layouts.app')
@section('title', 'Commande #'.$commande->id)

@push('styles')
<style>
.commande-wrapper { max-width:820px; margin:0 auto; padding:40px 16px; }
.page-title { font-size:1.4rem; font-weight:800; color:#1a1a2e; margin-bottom:28px; }
.card-section { background:#fff; border-radius:16px; box-shadow:0 1px 6px rgba(0,0,0,.07); padding:24px; margin-bottom:20px; }
.section-label { font-size:.75rem; font-weight:700; color:#aaa; text-transform:uppercase; letter-spacing:.5px; margin-bottom:14px; }
.item-row { display:flex; gap:14px; align-items:center; padding:12px 0; border-bottom:1px solid #f5f5f5; }
.item-row:last-child { border-bottom:none; }
.item-row-img { width:60px; height:60px; object-fit:cover; border-radius:10px; flex-shrink:0; }
.item-row-img-ph { width:60px; height:60px; border-radius:10px; background:#f3f4f6; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.item-row-info { flex:1; min-width:0; }
.item-row-title { font-weight:700; font-size:.88rem; color:#1a1a2e; margin-bottom:3px; }
.item-row-meta { font-size:.73rem; color:#aaa; }
.item-row-price { font-weight:800; font-size:.9rem; color:#16a34a; white-space:nowrap; }
.summary-line { display:flex; justify-content:space-between; font-size:.88rem; color:#555; margin-bottom:10px; }
.summary-total { display:flex; justify-content:space-between; font-size:1.1rem; font-weight:800; padding-top:14px; border-top:2px solid #f0f0f0; margin-top:10px; }

.pill { display:inline-flex; align-items:center; gap:5px; padding:4px 12px; border-radius:50px; font-size:.73rem; font-weight:700; }
.pill-orange { background:#fef9c3; color:#b45309; }
.pill-green  { background:#f0fdf4; color:#16a34a; }
.pill-red    { background:#fef2f2; color:#ef4444; }
.pill-blue   { background:#eff6ff; color:#1d4ed8; }
.pill-dot    { width:6px; height:6px; border-radius:50%; background:currentColor; display:inline-block; }
</style>
@endpush

@section('content')
<div class="commande-wrapper">
    <div class="d-flex align-items-center gap-3 mb-4 flex-wrap">
        <a href="{{ route('commandes.index') }}" class="btn btn-outline-secondary rounded-pill btn-sm">
            <i class="fas fa-arrow-left me-1"></i>Retour
        </a>
        <div class="page-title mb-0">Commande #{{ $commande->id }}</div>
        @php
            $pillMap = ['en_attente'=>'pill-orange','confirmée'=>'pill-green','annulée'=>'pill-red'];
            $labelMap = ['en_attente'=>'En attente','confirmée'=>'Confirmée','annulée'=>'Annulée'];
        @endphp
        <span class="pill {{ $pillMap[$commande->statut] ?? 'pill-gray' }}">
            <span class="pill-dot"></span>{{ $labelMap[$commande->statut] ?? $commande->statut }}
        </span>
        <span class="text-muted small ms-auto">{{ $commande->created_at->format('d/m/Y à H:i') }}</span>
    </div>

    @if(session('success'))
        <div class="alert alert-success rounded-3 mb-4">{{ session('success') }}</div>
    @endif

    {{-- Articles commandés --}}
    <div class="card-section">
        <div class="section-label"><i class="fas fa-box me-2"></i>Articles commandés</div>
        @foreach($commande->items as $item)
        <div class="item-row">
            @if($item->annonce && $item->annonce->photoPrincipale)
                <img src="{{ $item->annonce->photoPrincipale->url }}" class="item-row-img" alt="{{ $item->annonce->titre }}" onerror="this.src='{{ asset('img/no-image.jpg') }}'">
            @else
                <div class="item-row-img-ph"><i class="fas fa-box" style="color:#9ca3af;"></i></div>
            @endif

            <div class="item-row-info">
                <div class="item-row-title">
                    @if($item->annonce)
                        <a href="{{ route('annonces.show', $item->annonce) }}" class="text-dark text-decoration-none">{{ $item->annonce->titre }}</a>
                    @else
                        Article supprimé
                    @endif
                </div>
                <div class="item-row-meta">
                    <i class="fas fa-user me-1"></i>{{ $item->fournisseur->prenom ?? '—' }} {{ $item->fournisseur->nom ?? '' }}
                    <span class="ms-2">•</span>
                    <span class="ms-2">Qté : {{ $item->quantite }} {{ $item->annonce->unite ?? '' }}</span>
                    @if($item->prix_unitaire > 0)
                    <span class="ms-2">•</span>
                    <span class="ms-2">{{ number_format($item->prix_unitaire, 0, ',', ' ') }} F/{{ $item->annonce->unite ?? 'u.' }}</span>
                    @endif
                </div>
            </div>

            <div class="text-end">
                <div class="item-row-price">
                    @if($item->prix_unitaire == 0) Gratuit
                    @else {{ number_format($item->sousTotal(), 0, ',', ' ') }} F
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="row g-4">
        {{-- Résumé financier --}}
        <div class="col-md-6">
            <div class="card-section h-100">
                <div class="section-label"><i class="fas fa-receipt me-2"></i>Résumé</div>
                @foreach($commande->items as $item)
                <div class="summary-line">
                    <span style="max-width:160px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-size:.82rem;">{{ $item->annonce->titre ?? '—' }}</span>
                    <span>{{ $item->prix_unitaire == 0 ? 'Gratuit' : number_format($item->sousTotal(),0,',',' ').' F' }}</span>
                </div>
                @endforeach
                <div class="summary-total">
                    <span>Total</span>
                    <span style="color:#16a34a;">{{ $commande->montant_total > 0 ? number_format($commande->montant_total,0,',',' ').' FCFA' : 'Gratuit' }}</span>
                </div>
            </div>
        </div>

        {{-- Infos commande --}}
        <div class="col-md-6">
            <div class="card-section h-100">
                <div class="section-label"><i class="fas fa-info-circle me-2"></i>Informations</div>
                <div style="font-size:.85rem;color:#555;line-height:2;">
                    <div><strong>Date :</strong> {{ $commande->created_at->format('d/m/Y à H:i') }}</div>
                    @if($commande->adresse_livraison)
                    <div><strong>Adresse :</strong> {{ $commande->adresse_livraison }}</div>
                    @endif
                    @if($commande->message)
                    <div><strong>Message :</strong> {{ $commande->message }}</div>
                    @endif
                    <div><strong>Articles :</strong> {{ $commande->items->count() }}</div>
                    <div><strong>Fournisseurs :</strong> {{ $commande->items->pluck('fournisseur.prenom')->unique()->filter()->count() }}</div>
                </div>

                @if($commande->statut === 'en_attente')
                <form action="{{ route('commandes.annuler', $commande) }}" method="POST" class="mt-4">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger rounded-pill btn-sm w-100" onclick="return confirm('Annuler cette commande ?')">
                        <i class="fas fa-times me-1"></i>Annuler la commande
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>

    {{-- Contacter les fournisseurs --}}
    @php $fournisseurs = $commande->items->pluck('fournisseur')->unique('id')->filter(); @endphp
    @if($fournisseurs->count())
    <div class="card-section mt-4">
        <div class="section-label"><i class="fas fa-comments me-2"></i>Contacter un fournisseur</div>
        <div class="d-flex flex-wrap gap-2">
            @foreach($fournisseurs as $f)
            <a href="{{ route('messages.ouvrir', $f->id) }}" class="btn btn-outline-primary rounded-pill btn-sm">
                <i class="fas fa-comment me-1"></i>{{ $f->prenom }} {{ $f->nom }}
            </a>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection
