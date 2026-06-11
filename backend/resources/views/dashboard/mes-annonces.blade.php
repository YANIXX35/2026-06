@extends('layouts.dashboard')
@section('title', 'Mes articles')

@section('sidebar-nav')
<span class="nav-section">Principal</span>
<a href="{{ route('dashboard') }}" class="nav-item">
    <span class="nav-icon"><i class="fas fa-home"></i></span> Accueil
</a>
<a href="{{ route('annonces.create') }}" class="nav-item">
    <span class="nav-icon"><i class="fas fa-plus-circle"></i></span> Publier un surplus
</a>
<span class="nav-section">Gestion</span>
<a href="{{ route('annonces.mes-annonces') }}" class="nav-item active">
    <span class="nav-icon"><i class="fas fa-list"></i></span> Mes articles
    @if($annonces->total() > 0)<span class="badge" style="background:#16a34a;">{{ $annonces->total() }}</span>@endif
</a>
<a href="{{ route('reservations.mes-reservations') }}" class="nav-item">
    <span class="nav-icon"><i class="fas fa-handshake"></i></span> Réservations
</a>
<a href="{{ route('messages.index') }}" class="nav-item">
    <span class="nav-icon"><i class="fas fa-comments"></i></span> Messages
</a>
<span class="nav-section">Boutique</span>
<a href="{{ route('annonces.index') }}" class="nav-item">
    <span class="nav-icon"><i class="fas fa-store"></i></span> Explorer la boutique
</a>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1><i class="fas fa-list" style="color:#16a34a;font-size:1.1rem;margin-right:8px;"></i>Mes articles</h1>
        <p style="font-size:.85rem;color:#888;margin-top:4px;">Gérez tous vos surplus publiés sur la plateforme.</p>
    </div>
    <a href="{{ route('annonces.create') }}"
        style="display:flex;align-items:center;gap:8px;background:#16a34a;color:#fff;text-decoration:none;padding:10px 22px;border-radius:50px;font-size:.84rem;font-weight:600;">
        <i class="fas fa-plus"></i> Publier un nouveau surplus
    </a>
</div>

@if(session('success'))
<div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;padding:12px 18px;margin-bottom:16px;font-size:.84rem;color:#15803d;font-weight:500;">
    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
</div>
@endif

<div class="card">
    @if($annonces->isEmpty())
    <div style="text-align:center;padding:60px 20px;color:#bbb;">
        <div style="font-size:3rem;margin-bottom:14px;">📦</div>
        <p style="font-size:.9rem;font-weight:600;color:#999;margin-bottom:6px;">Aucun article pour l'instant</p>
        <p style="font-size:.8rem;margin-bottom:20px;">Publiez votre premier surplus et contribuez à réduire le gaspillage.</p>
        <a href="{{ route('annonces.create') }}"
            style="background:#16a34a;color:#fff;padding:10px 28px;border-radius:50px;text-decoration:none;font-size:.84rem;font-weight:600;">
            <i class="fas fa-plus me-2"></i>Publier mon premier article
        </a>
    </div>
    @else
    <table class="dash-table">
        <thead>
            <tr>
                <th style="width:50px;">Photo</th>
                <th>Article</th>
                <th>Catégorie</th>
                <th>Type</th>
                <th>Prix</th>
                <th>Statut</th>
                <th>Résa.</th>
                <th>Vues</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        @foreach($annonces as $annonce)
        <tr>
            <td>
                @if($annonce->photoPrincipale)
                <img src="{{ $annonce->photoPrincipale->url }}"
                    style="width:40px;height:40px;object-fit:cover;border-radius:8px;border:1.5px solid #f0f0f0;"
                    onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                <div style="display:none;width:40px;height:40px;background:#f9fafb;border-radius:8px;align-items:center;justify-content:center;font-size:1.1rem;">{{ $annonce->categorie->icone ?? '📦' }}</div>
                @else
                <div style="width:40px;height:40px;background:#f9fafb;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:1.1rem;">{{ $annonce->categorie->icone ?? '📦' }}</div>
                @endif
            </td>
            <td>
                <div style="font-weight:600;font-size:.84rem;">{{ Str::limit($annonce->titre, 30) }}</div>
                <div style="font-size:.72rem;color:#aaa;margin-top:2px;">
                    <i class="fas fa-map-marker-alt" style="color:#bbb;"></i> {{ Str::limit($annonce->adresse_collecte ?? '—', 22) }}
                </div>
            </td>
            <td style="color:#888;font-size:.78rem;">{{ $annonce->categorie->icone ?? '' }} {{ $annonce->categorie->nom ?? '—' }}</td>
            <td>
                @php $tc=['vente'=>'pill-blue','don'=>'pill-green','alimentation_animale'=>'pill-yellow','transformation'=>'pill-gray']; @endphp
                <span class="status-pill {{ $tc[$annonce->type_offre]??'pill-gray' }}" style="font-size:.7rem;">{{ $annonce->type_offre }}</span>
            </td>
            <td style="font-weight:600;font-size:.82rem;color:#16a34a;">
                {{ $annonce->type_offre==='don' ? 'Gratuit' : ($annonce->prix > 0 ? number_format($annonce->prix, 0, ',', ' ').' F' : 'À discuter') }}
            </td>
            <td>
                @php $sc=['disponible'=>'pill-green','reservé'=>'pill-yellow','expiré'=>'pill-gray','supprimé'=>'pill-red']; @endphp
                <span class="status-pill {{ $sc[$annonce->statut]??'pill-gray' }}"><span class="pill-dot"></span>{{ $annonce->statut }}</span>
            </td>
            <td style="text-align:center;">
                <span style="background:#f0fdf4;color:#16a34a;font-size:.72rem;font-weight:700;padding:3px 8px;border-radius:50px;">
                    {{ $annonce->reservations->count() }}
                </span>
            </td>
            <td style="color:#888;font-size:.78rem;">
                <i class="fas fa-eye" style="margin-right:3px;font-size:.7rem;"></i>{{ $annonce->vues }}
            </td>
            <td>
                <div style="display:flex;gap:8px;align-items:center;">
                    <a href="{{ route('annonces.edit', $annonce) }}" title="Modifier"
                        style="width:28px;height:28px;background:#f0fdf4;color:#16a34a;border-radius:6px;display:flex;align-items:center;justify-content:center;font-size:.75rem;text-decoration:none;">
                        <i class="fas fa-edit"></i>
                    </a>
                    <a href="{{ route('annonces.show', $annonce) }}" title="Voir"
                        style="width:28px;height:28px;background:#f5f5f5;color:#888;border-radius:6px;display:flex;align-items:center;justify-content:center;font-size:.75rem;text-decoration:none;">
                        <i class="fas fa-eye"></i>
                    </a>
                    @if($annonce->statut !== 'supprimé')
                    <form action="{{ route('annonces.destroy', $annonce) }}" method="POST" onsubmit="return confirm('Supprimer cet article ?');" style="display:inline;">
                        @csrf @method('DELETE')
                        <button type="submit" title="Supprimer"
                            style="width:28px;height:28px;background:#fef2f2;color:#ef4444;border:none;border-radius:6px;display:flex;align-items:center;justify-content:center;font-size:.75rem;cursor:pointer;">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                    @endif
                </div>
            </td>
        </tr>
        @endforeach
        </tbody>
    </table>

    @if($annonces->hasPages())
    <div style="padding:16px 20px;border-top:1px solid #f5f5f5;display:flex;justify-content:space-between;align-items:center;">
        <span style="font-size:.75rem;color:#bbb;">Affichage {{ $annonces->firstItem() }}–{{ $annonces->lastItem() }} sur {{ $annonces->total() }} articles</span>
        <div style="display:flex;gap:4px;">
            @if($annonces->onFirstPage())
            <span style="padding:5px 12px;border-radius:6px;background:#f5f5f5;color:#ccc;font-size:.75rem;">‹ Préc.</span>
            @else
            <a href="{{ $annonces->previousPageUrl() }}" style="padding:5px 12px;border-radius:6px;background:#f5f5f5;color:#888;font-size:.75rem;text-decoration:none;">‹ Préc.</a>
            @endif
            @if($annonces->hasMorePages())
            <a href="{{ $annonces->nextPageUrl() }}" style="padding:5px 12px;border-radius:6px;background:#16a34a;color:#fff;font-size:.75rem;text-decoration:none;">Suiv. ›</a>
            @else
            <span style="padding:5px 12px;border-radius:6px;background:#f5f5f5;color:#ccc;font-size:.75rem;">Suiv. ›</span>
            @endif
        </div>
    </div>
    @endif
    @endif
</div>
@endsection

@section('right-panel')
<div class="card" style="margin-bottom:16px;">
    <div style="padding:16px 18px;border-bottom:1px solid #f5f5f5;">
        <div style="font-size:.82rem;font-weight:700;color:#1a1a2e;"><i class="fas fa-chart-bar me-2" style="color:#16a34a;"></i>Résumé</div>
    </div>
    <div style="padding:16px 18px;display:flex;flex-direction:column;gap:10px;">
        @php
            $dispo = $annonces->getCollection()->where('statut','disponible')->count();
            $res = $annonces->getCollection()->where('statut','reservé')->count();
            $exp = $annonces->getCollection()->where('statut','expiré')->count();
        @endphp
        <div style="display:flex;justify-content:space-between;font-size:.78rem;">
            <span style="color:#888;">Total (cette page)</span>
            <span style="font-weight:700;color:#1a1a2e;">{{ $annonces->count() }}</span>
        </div>
        <div style="display:flex;justify-content:space-between;font-size:.78rem;">
            <span style="color:#888;">Disponibles</span>
            <span class="status-pill pill-green" style="font-size:.65rem;padding:2px 8px;">{{ $dispo }}</span>
        </div>
        <div style="display:flex;justify-content:space-between;font-size:.78rem;">
            <span style="color:#888;">Réservés</span>
            <span class="status-pill pill-yellow" style="font-size:.65rem;padding:2px 8px;">{{ $res }}</span>
        </div>
        <div style="display:flex;justify-content:space-between;font-size:.78rem;">
            <span style="color:#888;">Expirés</span>
            <span class="status-pill pill-gray" style="font-size:.65rem;padding:2px 8px;">{{ $exp }}</span>
        </div>
    </div>
</div>

<div class="card">
    <div style="padding:16px 18px;border-bottom:1px solid #f5f5f5;">
        <div style="font-size:.82rem;font-weight:700;color:#1a1a2e;"><i class="fas fa-bolt me-2" style="color:#f59e0b;"></i>Actions rapides</div>
    </div>
    <div style="padding:14px 18px;display:flex;flex-direction:column;gap:8px;">
        <a href="{{ route('annonces.create') }}"
            style="display:flex;align-items:center;gap:8px;padding:10px 14px;background:#f0fdf4;border-radius:8px;text-decoration:none;color:#16a34a;font-size:.78rem;font-weight:600;">
            <i class="fas fa-plus-circle"></i> Publier un nouveau surplus
        </a>
        <a href="{{ route('reservations.mes-reservations') }}"
            style="display:flex;align-items:center;gap:8px;padding:10px 14px;background:#fef9c3;border-radius:8px;text-decoration:none;color:#b45309;font-size:.78rem;font-weight:600;">
            <i class="fas fa-handshake"></i> Gérer les réservations
        </a>
        <a href="{{ route('annonces.index') }}"
            style="display:flex;align-items:center;gap:8px;padding:10px 14px;background:#f5f5f5;border-radius:8px;text-decoration:none;color:#888;font-size:.78rem;font-weight:600;">
            <i class="fas fa-store"></i> Voir la boutique publique
        </a>
    </div>
</div>
@endsection
