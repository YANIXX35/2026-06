@extends('layouts.dashboard')
@section('title','Annonces')
@section('page-title','Annonces')

@section('sidebar-nav')
<span class="nav-section">Vue globale</span>
<a href="{{ route('admin.dashboard') }}" class="nav-item">
    <span class="nav-icon"><i class="fas fa-th-large"></i></span> Tableau de bord
</a>
<span class="nav-section">Gestion</span>
<a href="{{ route('admin.utilisateurs') }}" class="nav-item">
    <span class="nav-icon"><i class="fas fa-users"></i></span> Utilisateurs
</a>
<a href="{{ route('admin.annonces') }}" class="nav-item active">
    <span class="nav-icon"><i class="fas fa-bullhorn"></i></span> Annonces
</a>
<a href="{{ route('admin.categories.index') }}" class="nav-item">
    <span class="nav-icon"><i class="fas fa-tags"></i></span> Catégories
</a>
<a href="{{ route('admin.signalements') }}" class="nav-item">
    <span class="nav-icon"><i class="fas fa-flag"></i></span> Signalements
</a>
<span class="nav-section">Plateforme</span>
<a href="{{ route('annonces.index') }}" class="nav-item">
    <span class="nav-icon"><i class="fas fa-globe"></i></span> Voir le site
</a>
@endsection

@section('content')

<div class="page-header">
    <div>
        <h1>Annonces <span style="color:var(--green);">publiées</span></h1>
        <p style="font-size:.82rem;color:var(--muted);margin-top:4px;">{{ $annonces->total() }} annonces sur la plateforme.</p>
    </div>
    <a href="{{ route('admin.dashboard') }}" style="display:flex;align-items:center;gap:7px;background:#fff;border:1px solid var(--border);padding:9px 18px;border-radius:50px;font-size:.8rem;font-weight:700;color:var(--muted);text-decoration:none;transition:all .2s;">
        <i class="fas fa-arrow-left"></i> Retour
    </a>
</div>

{{-- STATS --}}
@php
    $total      = $annonces->total();
    $disponibles= \App\Models\Annonce::where('statut','disponible')->count();
    $reserves   = \App\Models\Annonce::where('statut','reservé')->count();
    $expires    = \App\Models\Annonce::where('statut','expiré')->count();
@endphp
<div class="stats-row" style="margin-bottom:20px;">
    <div class="stat-card" style="--sc-clr:linear-gradient(90deg,#6366f1,#818cf8);">
        <div class="stat-icon" style="background:#ede9fe;"><i class="fas fa-bullhorn" style="color:#6366f1;"></i></div>
        <div><div class="stat-value">{{ $total }}</div><div class="stat-label">Total</div></div>
    </div>
    <div class="stat-card" style="--sc-clr:linear-gradient(90deg,#16a34a,#22c55e);">
        <div class="stat-icon" style="background:#f0fdf4;"><i class="fas fa-check-circle" style="color:#16a34a;"></i></div>
        <div><div class="stat-value">{{ $disponibles }}</div><div class="stat-label">Disponibles</div></div>
    </div>
    <div class="stat-card" style="--sc-clr:linear-gradient(90deg,#f59e0b,#fbbf24);">
        <div class="stat-icon" style="background:#fef9c3;"><i class="fas fa-clock" style="color:#d97706;"></i></div>
        <div><div class="stat-value">{{ $reserves }}</div><div class="stat-label">Réservées</div></div>
    </div>
    <div class="stat-card" style="--sc-clr:linear-gradient(90deg,#94a3b8,#cbd5e1);">
        <div class="stat-icon" style="background:#f1f5f9;"><i class="fas fa-archive" style="color:#64748b;"></i></div>
        <div><div class="stat-value">{{ $expires }}</div><div class="stat-label">Expirées</div></div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title"><i class="fas fa-bullhorn" style="color:#f97316;font-size:.85rem;"></i> Toutes les annonces</span>
    </div>
    <table class="dash-table">
        <thead>
            <tr>
                <th>Titre</th>
                <th>Fournisseur</th>
                <th>Catégorie</th>
                <th>Type</th>
                <th>Statut</th>
                <th>Vues</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        @foreach($annonces as $annonce)
        <tr>
            <td style="font-weight:700;max-width:180px;">{{ Str::limit($annonce->titre, 30) }}</td>
            <td style="color:var(--muted);font-size:.79rem;">{{ $annonce->user->prenom ?? '—' }} {{ $annonce->user->nom ?? '' }}</td>
            <td style="font-size:.78rem;color:var(--muted);">{{ $annonce->categorie->nom ?? '—' }}</td>
            <td>
                @php $tc=['vente'=>'pill-blue','don'=>'pill-green','alimentation_animale'=>'pill-yellow','transformation'=>'pill-purple']; @endphp
                <span class="status-pill {{ $tc[$annonce->type_offre] ?? 'pill-gray' }}">{{ $annonce->type_offre }}</span>
            </td>
            <td>
                @php $sc=['disponible'=>'pill-green','reservé'=>'pill-yellow','expiré'=>'pill-gray','supprimé'=>'pill-red']; @endphp
                <span class="status-pill {{ $sc[$annonce->statut] ?? 'pill-gray' }}">
                    <span class="pill-dot"></span>{{ $annonce->statut }}
                </span>
            </td>
            <td style="color:var(--muted);font-size:.78rem;">{{ $annonce->vues }}</td>
            <td style="color:var(--muted);font-size:.73rem;">{{ $annonce->created_at->format('d/m/Y') }}</td>
            <td>
                <div style="display:flex;gap:6px;align-items:center;">
                    <a href="{{ route('annonces.show', $annonce) }}"
                        style="width:28px;height:28px;border-radius:7px;border:1px solid var(--border);background:var(--surface);
                            display:inline-flex;align-items:center;justify-content:center;
                            color:var(--muted);font-size:.75rem;text-decoration:none;transition:all .2s;"
                        onmouseover="this.style.background='var(--green-50)';this.style.color='var(--green)'"
                        onmouseout="this.style.background='var(--surface)';this.style.color='var(--muted)'">
                        <i class="fas fa-eye"></i>
                    </a>
                    @if($annonce->statut !== 'supprimé')
                    <form action="{{ route('admin.supprimer-annonce', $annonce) }}" method="POST"
                        onsubmit="return confirm('Supprimer cette annonce ?')">
                        @csrf @method('DELETE')
                        <button style="width:28px;height:28px;border-radius:7px;border:1px solid #fecaca;background:#fff;
                            display:inline-flex;align-items:center;justify-content:center;
                            color:#ef4444;font-size:.75rem;cursor:pointer;transition:all .2s;"
                            onmouseover="this.style.background='#fef2f2'" onmouseout="this.style.background='#fff'">
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
    <div style="padding:16px 22px;border-top:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;">
        <span style="font-size:.74rem;color:var(--muted);">{{ $annonces->firstItem() }}–{{ $annonces->lastItem() }} sur {{ $annonces->total() }}</span>
        <div style="display:flex;gap:6px;">
            @if($annonces->onFirstPage())
                <span style="padding:6px 12px;border:1px solid var(--border);border-radius:8px;font-size:.75rem;color:var(--muted-2);cursor:default;">←</span>
            @else
                <a href="{{ $annonces->previousPageUrl() }}" style="padding:6px 12px;border:1px solid var(--border);border-radius:8px;font-size:.75rem;color:var(--text);text-decoration:none;">←</a>
            @endif
            @if($annonces->hasMorePages())
                <a href="{{ $annonces->nextPageUrl() }}" style="padding:6px 12px;border:1px solid var(--border);border-radius:8px;font-size:.75rem;color:var(--text);text-decoration:none;">→</a>
            @else
                <span style="padding:6px 12px;border:1px solid var(--border);border-radius:8px;font-size:.75rem;color:var(--muted-2);cursor:default;">→</span>
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
    <div class="activity-title">Par type d'offre</div>
    @foreach([['vente','#dbeafe','#1d4ed8'],['don','#dcfce7','#15803d'],['alimentation_animale','#fef9c3','#92400e'],['transformation','#f3e8ff','#6d28d9']] as $t)
    <div class="activity-item">
        <div class="activity-avatar" style="background:{{ $t[1] }};color:{{ $t[2] }};border-color:{{ $t[1] }};">
            {{ strtoupper(substr($t[0],0,1)) }}
        </div>
        <div class="activity-text">
            <strong>{{ ucfirst($t[0]) }}</strong>
            <div class="activity-time">{{ \App\Models\Annonce::where('type_offre',$t[0])->count() }} annonces</div>
        </div>
    </div>
    @endforeach
</div>
@endsection
