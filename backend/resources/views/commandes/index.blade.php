@extends('layouts.app')
@section('title', 'Mes commandes')

@push('styles')
<style>
.commandes-wrapper { max-width:900px; margin:0 auto; padding:40px 16px; }
.page-title { font-size:1.5rem; font-weight:800; color:#1a1a2e; margin-bottom:28px; display:flex; align-items:center; gap:12px; }
.commande-card { background:#fff; border-radius:16px; box-shadow:0 1px 6px rgba(0,0,0,.07); margin-bottom:16px; overflow:hidden; transition:.2s; }
.commande-card:hover { box-shadow:0 4px 18px rgba(0,0,0,.1); }
.commande-header { padding:16px 22px; display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid #f5f5f5; flex-wrap:wrap; gap:8px; }
.commande-num { font-size:.82rem; font-weight:700; color:#1a1a2e; }
.commande-date { font-size:.72rem; color:#aaa; }
.commande-body { padding:16px 22px; display:flex; gap:10px; align-items:center; flex-wrap:wrap; }
.item-thumb { width:48px; height:48px; border-radius:8px; object-fit:cover; border:1.5px solid #f0f0f0; }
.item-thumb-placeholder { width:48px; height:48px; border-radius:8px; background:#f3f4f6; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.more-badge { width:48px; height:48px; border-radius:8px; background:#f0fdf4; display:flex; align-items:center; justify-content:center; font-size:.75rem; font-weight:700; color:#16a34a; flex-shrink:0; }
.commande-footer { padding:12px 22px; background:#fafafa; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:8px; }
.commande-total { font-size:.95rem; font-weight:800; color:#16a34a; }
.btn-detail { display:inline-flex; align-items:center; gap:6px; background:#16a34a; color:#fff; padding:7px 18px; border-radius:50px; text-decoration:none; font-size:.78rem; font-weight:700; transition:.2s; }
.btn-detail:hover { background:#15803d; color:#fff; }
.btn-annuler { background:#fef2f2; color:#ef4444; border:none; padding:7px 16px; border-radius:50px; font-size:.78rem; font-weight:600; cursor:pointer; }

/* statut pills */
.pill { display:inline-flex; align-items:center; gap:5px; padding:4px 12px; border-radius:50px; font-size:.73rem; font-weight:700; }
.pill-orange { background:#fef9c3; color:#b45309; }
.pill-green  { background:#f0fdf4; color:#16a34a; }
.pill-red    { background:#fef2f2; color:#ef4444; }
.pill-blue   { background:#eff6ff; color:#1d4ed8; }
.pill-gray   { background:#f3f4f6; color:#6b7280; }
.pill-dot    { width:6px; height:6px; border-radius:50%; background:currentColor; display:inline-block; }
</style>
@endpush

@section('content')
<div class="commandes-wrapper">
    <div class="page-title">
        <span style="background:#eff6ff;width:44px;height:44px;border-radius:12px;display:flex;align-items:center;justify-content:center;">
            <i class="fas fa-box" style="color:#1d4ed8;font-size:1.1rem;"></i>
        </span>
        Mes commandes
    </div>

    @if(session('success'))
        <div class="alert alert-success rounded-3 mb-4">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger rounded-3 mb-4">{{ session('error') }}</div>
    @endif

    @forelse($commandes as $commande)
    @php
        $pillMap = [
            'en_attente' => 'pill-orange',
            'confirmée'  => 'pill-green',
            'annulée'    => 'pill-red',
        ];
        $labelMap = [
            'en_attente' => 'En attente',
            'confirmée'  => 'Confirmée',
            'annulée'    => 'Annulée',
        ];
        $pillClass = $pillMap[$commande->statut] ?? 'pill-gray';
        $label     = $labelMap[$commande->statut] ?? ucfirst($commande->statut);
        $displayItems = $commande->items->take(4);
        $reste = $commande->items->count() - 4;
    @endphp
    <div class="commande-card">
        <div class="commande-header">
            <div>
                <div class="commande-num"><i class="fas fa-hashtag me-1" style="color:#16a34a;"></i>Commande #{{ $commande->id }}</div>
                <div class="commande-date">{{ $commande->created_at->format('d/m/Y à H:i') }}</div>
            </div>
            <span class="pill {{ $pillClass }}"><span class="pill-dot"></span>{{ $label }}</span>
        </div>

        <div class="commande-body">
            @foreach($displayItems as $item)
                @if($item->annonce && $item->annonce->photoPrincipale)
                    <img src="{{ $item->annonce->photoPrincipale->url }}" class="item-thumb" alt="{{ $item->annonce->titre }}" title="{{ $item->annonce->titre }}" onerror="this.src='{{ asset('img/no-image.jpg') }}'">
                @else
                    <div class="item-thumb-placeholder" title="{{ $item->annonce->titre ?? '—' }}">
                        <i class="fas fa-box" style="color:#9ca3af;"></i>
                    </div>
                @endif
            @endforeach
            @if($reste > 0)
                <div class="more-badge">+{{ $reste }}</div>
            @endif
            <div style="flex:1;min-width:150px;">
                <div style="font-size:.82rem;color:#555;">
                    {{ $commande->items->count() }} article(s) •
                    {{ $commande->items->pluck('fournisseur.prenom')->unique()->filter()->implode(', ') ?: '—' }}
                </div>
                @if($commande->adresse_livraison)
                <div style="font-size:.72rem;color:#aaa;margin-top:3px;"><i class="fas fa-map-marker-alt me-1"></i>{{ $commande->adresse_livraison }}</div>
                @endif
            </div>
        </div>

        <div class="commande-footer">
            <div class="commande-total">
                @if(->mode_paiement)
                    @if(->mode_paiement === 'wave')
                        <span style="font-size:.7rem;font-weight:700;background:#fff8f3;color:#c2410c;border:1px solid #fed7aa;padding:2px 8px;border-radius:50px;margin-right:8px;"><i class="fas fa-bolt"></i> Wave</span>
                    @elseif(->mode_paiement === 'moov_money')
                        <span style="font-size:.7rem;font-weight:700;background:#eff6ff;color:#1d4ed8;border:1px solid #bfdbfe;padding:2px 8px;border-radius:50px;margin-right:8px;"><i class="fas fa-mobile-alt"></i> Moov</span>
                    @endif
                @endif
                {{ $commande->montant_total > 0 ? number_format($commande->montant_total, 0, ',', ' ').' FCFA' : 'Gratuit' }}
            </div>
            <div class="d-flex gap-2 align-items-center">
                @if($commande->statut === 'en_attente')
                <form action="{{ route('commandes.annuler', $commande) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn-annuler" onclick="return confirm('Annuler cette commande ?')">
                        <i class="fas fa-times me-1"></i>Annuler
                    </button>
                </form>
                @endif
                <a href="{{ route('commandes.show', $commande) }}" class="btn-detail">
                    <i class="fas fa-eye"></i> Voir le détail
                </a>
            </div>
        </div>
    </div>
    @empty
    <div class="text-center py-5">
        <i class="fas fa-box-open fa-4x text-muted mb-4 opacity-25"></i>
        <h5 class="text-muted">Aucune commande pour l'instant</h5>
        <p class="text-muted mb-4">Ajoutez des produits au panier et passez votre première commande.</p>
        <a href="{{ route('cart.index') }}" class="btn btn-primary rounded-pill px-5 py-2">
            <i class="fas fa-shopping-cart me-2"></i>Voir mon panier
        </a>
    </div>
    @endforelse

    <div class="mt-4">{{ $commandes->links() }}</div>
</div>
@endsection
