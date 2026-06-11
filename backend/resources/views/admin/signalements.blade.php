@extends('layouts.dashboard')
@section('title','Signalements')
@section('page-title','Signalements')

@section('sidebar-nav')
<span class="nav-section">Vue globale</span>
<a href="{{ route('admin.dashboard') }}" class="nav-item">
    <span class="nav-icon"><i class="fas fa-th-large"></i></span> Tableau de bord
</a>
<span class="nav-section">Gestion</span>
<a href="{{ route('admin.utilisateurs') }}" class="nav-item">
    <span class="nav-icon"><i class="fas fa-users"></i></span> Utilisateurs
</a>
<a href="{{ route('admin.annonces') }}" class="nav-item">
    <span class="nav-icon"><i class="fas fa-bullhorn"></i></span> Annonces
</a>
<a href="{{ route('admin.categories.index') }}" class="nav-item">
    <span class="nav-icon"><i class="fas fa-tags"></i></span> Catégories
</a>
<a href="{{ route('admin.signalements') }}" class="nav-item active">
    <span class="nav-icon"><i class="fas fa-flag"></i></span> Signalements
    @php $pending = $signalements->where('statut','en_attente')->count(); @endphp
    @if($pending > 0)
    <span class="nav-badge" style="background:#ef4444;">{{ $pending }}</span>
    @endif
</a>
<span class="nav-section">Plateforme</span>
<a href="{{ route('annonces.index') }}" class="nav-item">
    <span class="nav-icon"><i class="fas fa-globe"></i></span> Voir le site
</a>
@endsection

@section('content')

<div class="page-header">
    <div>
        <h1>Signalements <span style="color:var(--green);">à traiter</span></h1>
        <p style="font-size:.82rem;color:var(--muted);margin-top:4px;">Modération de la plateforme AntiGaspiCI.</p>
    </div>
    <a href="{{ route('admin.dashboard') }}" style="display:flex;align-items:center;gap:7px;background:#fff;border:1px solid var(--border);padding:9px 18px;border-radius:50px;font-size:.8rem;font-weight:700;color:var(--muted);text-decoration:none;">
        <i class="fas fa-arrow-left"></i> Retour
    </a>
</div>

{{-- STATS --}}
@php
    $enAttente = $signalements->where('statut','en_attente')->count();
    $traites   = $signalements->where('statut','traité')->count();
    $rejetes   = $signalements->where('statut','rejeté')->count();
@endphp
<div class="stats-row" style="margin-bottom:20px;grid-template-columns:repeat(3,1fr);">
    <div class="stat-card" style="--sc-clr:linear-gradient(90deg,#ef4444,#f87171);">
        <div class="stat-icon" style="background:#fee2e2;"><i class="fas fa-exclamation-triangle" style="color:#ef4444;"></i></div>
        <div><div class="stat-value">{{ $enAttente }}</div><div class="stat-label">En attente</div></div>
    </div>
    <div class="stat-card" style="--sc-clr:linear-gradient(90deg,#16a34a,#22c55e);">
        <div class="stat-icon" style="background:#f0fdf4;"><i class="fas fa-check-circle" style="color:#16a34a;"></i></div>
        <div><div class="stat-value">{{ $traites }}</div><div class="stat-label">Traités</div></div>
    </div>
    <div class="stat-card" style="--sc-clr:linear-gradient(90deg,#94a3b8,#cbd5e1);">
        <div class="stat-icon" style="background:#f1f5f9;"><i class="fas fa-times-circle" style="color:#64748b;"></i></div>
        <div><div class="stat-value">{{ $rejetes }}</div><div class="stat-label">Rejetés</div></div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title"><i class="fas fa-flag" style="color:#ef4444;font-size:.85rem;"></i> Tous les signalements</span>
    </div>

    @forelse($signalements as $sig)
    <div style="padding:16px 22px;border-bottom:1px solid #f1f5f9;transition:background .15s;"
        onmouseover="this.style.background='#fafafa'" onmouseout="this.style.background='#fff'">
        <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:16px;">
            <div style="flex:1;min-width:0;">
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:5px;flex-wrap:wrap;">
                    @php $bc=['en_attente'=>['pill-red','🔴'],'traité'=>['pill-green','✅'],'rejeté'=>['pill-gray','⚫']]; @endphp
                    <span style="font-size:.85rem;font-weight:700;color:var(--text);">{{ $sig->raison }}</span>
                    <span class="status-pill {{ $bc[$sig->statut][0] ?? 'pill-gray' }}">
                        <span class="pill-dot"></span>{{ $sig->statut }}
                    </span>
                </div>
                @if($sig->description)
                <p style="font-size:.78rem;color:var(--muted);margin-bottom:6px;line-height:1.55;">{{ $sig->description }}</p>
                @endif
                <div style="font-size:.72rem;color:var(--muted-2);display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
                    <span><i class="fas fa-user" style="margin-right:4px;"></i>{{ $sig->auteur->prenom ?? '—' }} {{ $sig->auteur->nom ?? '' }}</span>
                    @if($sig->annonce)
                    <span>•</span>
                    <a href="{{ route('annonces.show', $sig->annonce) }}"
                        style="color:var(--green);text-decoration:none;font-weight:600;">
                        <i class="fas fa-bullhorn" style="margin-right:3px;"></i>{{ Str::limit($sig->annonce->titre, 35) }}
                    </a>
                    @endif
                    <span>•</span>
                    <span><i class="fas fa-clock" style="margin-right:3px;"></i>{{ $sig->created_at->diffForHumans() }}</span>
                </div>
            </div>
            @if($sig->statut === 'en_attente')
            <form action="{{ route('admin.traiter-signalement', $sig) }}" method="POST" style="flex-shrink:0;">
                @csrf
                <button style="background:var(--green-50);border:1px solid var(--green-100);color:var(--green);
                    padding:8px 16px;border-radius:10px;font-size:.75rem;cursor:pointer;
                    font-family:'Nunito Sans',sans-serif;font-weight:700;white-space:nowrap;transition:all .2s;"
                    onmouseover="this.style.background='var(--green-100)'" onmouseout="this.style.background='var(--green-50)'">
                    <i class="fas fa-check"></i> Marquer traité
                </button>
            </form>
            @else
            <span style="font-size:.72rem;color:var(--muted-2);font-style:italic;flex-shrink:0;">{{ ucfirst($sig->statut) }}</span>
            @endif
        </div>
    </div>
    @empty
    <div style="text-align:center;padding:60px 20px;">
        <i class="fas fa-check-circle" style="font-size:3rem;color:var(--green);opacity:.4;display:block;margin-bottom:12px;"></i>
        <div style="font-size:.9rem;font-weight:700;color:var(--muted);">Aucun signalement</div>
        <div style="font-size:.78rem;color:var(--muted-2);margin-top:4px;">La plateforme est saine !</div>
    </div>
    @endforelse

    @if(method_exists($signalements,'hasPages') && $signalements->hasPages())
    <div style="padding:16px 22px;border-top:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;">
        <span style="font-size:.74rem;color:var(--muted);">{{ $signalements->firstItem() }}–{{ $signalements->lastItem() }} sur {{ $signalements->total() }}</span>
        <div style="display:flex;gap:6px;">
            @if($signalements->onFirstPage())
                <span style="padding:6px 12px;border:1px solid var(--border);border-radius:8px;font-size:.75rem;color:var(--muted-2);">←</span>
            @else
                <a href="{{ $signalements->previousPageUrl() }}" style="padding:6px 12px;border:1px solid var(--border);border-radius:8px;font-size:.75rem;color:var(--text);text-decoration:none;">←</a>
            @endif
            @if($signalements->hasMorePages())
                <a href="{{ $signalements->nextPageUrl() }}" style="padding:6px 12px;border:1px solid var(--border);border-radius:8px;font-size:.75rem;color:var(--text);text-decoration:none;">→</a>
            @else
                <span style="padding:6px 12px;border:1px solid var(--border);border-radius:8px;font-size:.75rem;color:var(--muted-2);">→</span>
            @endif
        </div>
    </div>
    @endif
</div>

@endsection

@section('right-panel')
<div class="profile-card">
    <div class="profile-avatar" style="background:linear-gradient(135deg,#6366f1,#818cf8);">🛡️</div>
    <div class="profile-name">{{ Auth::user()->prenom ?? 'Admin' }} {{ Auth::user()->nom ?? '' }}</div>
    <div class="profile-role-badge">Administrateur</div>
    <div class="profile-actions">
        <a href="{{ route('admin.utilisateurs') }}" class="profile-btn" title="Utilisateurs"><i class="fas fa-users"></i></a>
        <a href="{{ route('admin.signalements') }}" class="profile-btn" title="Signalements"><i class="fas fa-flag"></i></a>
        <a href="{{ route('admin.annonces') }}" class="profile-btn" title="Annonces"><i class="fas fa-bullhorn"></i></a>
    </div>
</div>
<div class="activity-card">
    <div class="activity-title">État de la modération</div>
    @if($enAttente > 0)
    <div style="background:#fef2f2;border-radius:10px;padding:14px;border:1px solid #fecaca;margin-bottom:12px;">
        <div style="font-size:.78rem;font-weight:800;color:#b91c1c;margin-bottom:4px;">
            <i class="fas fa-exclamation-triangle"></i> {{ $enAttente }} signalement(s) en attente
        </div>
        <div style="font-size:.72rem;color:#ef4444;">Action requise immédiatement</div>
    </div>
    @else
    <div style="background:#f0fdf4;border-radius:10px;padding:14px;border:1px solid #bbf7d0;margin-bottom:12px;">
        <div style="font-size:.78rem;font-weight:800;color:#15803d;">
            <i class="fas fa-check-circle"></i> Tout est traité
        </div>
        <div style="font-size:.72rem;color:var(--green);">Plateforme saine</div>
    </div>
    @endif
    <div class="activity-item">
        <div class="activity-avatar" style="background:#fee2e2;color:#ef4444;border-color:#fecaca;">E</div>
        <div class="activity-text"><strong>En attente</strong><div class="activity-time">{{ $enAttente }} signalement(s)</div></div>
    </div>
    <div class="activity-item">
        <div class="activity-avatar" style="background:#dcfce7;color:#16a34a;border-color:#bbf7d0;">T</div>
        <div class="activity-text"><strong>Traités</strong><div class="activity-time">{{ $traites }} signalement(s)</div></div>
    </div>
    <div class="activity-item">
        <div class="activity-avatar" style="background:#f1f5f9;color:#64748b;border-color:#e2e8f0;">R</div>
        <div class="activity-text"><strong>Rejetés</strong><div class="activity-time">{{ $rejetes }} signalement(s)</div></div>
    </div>
</div>
@endsection
