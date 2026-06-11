@extends('layouts.dashboard')
@section('title','Utilisateurs')
@section('page-title','Utilisateurs')

@section('sidebar-nav')
<span class="nav-section">Vue globale</span>
<a href="{{ route('admin.dashboard') }}" class="nav-item">
    <span class="nav-icon"><i class="fas fa-th-large"></i></span> Tableau de bord
</a>
<span class="nav-section">Gestion</span>
<a href="{{ route('admin.utilisateurs') }}" class="nav-item active">
    <span class="nav-icon"><i class="fas fa-users"></i></span> Utilisateurs
</a>
<a href="{{ route('admin.annonces') }}" class="nav-item">
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
        <h1>Utilisateurs <span style="color:var(--green);">inscrits</span></h1>
        <p style="font-size:.82rem;color:var(--muted);margin-top:4px;">{{ $utilisateurs->total() }} membres sur la plateforme.</p>
    </div>
    <a href="{{ route('admin.dashboard') }}" style="display:flex;align-items:center;gap:7px;background:#fff;border:1px solid var(--border);padding:9px 18px;border-radius:50px;font-size:.8rem;font-weight:700;color:var(--muted);text-decoration:none;transition:all .2s;">
        <i class="fas fa-arrow-left"></i> Retour
    </a>
</div>

{{-- STATS RAPIDES --}}
@php
    $total     = $utilisateurs->total();
    $fournisseurs = \App\Models\User::where('role','fournisseur')->count();
    $acheteurs = \App\Models\User::where('role','acheteur')->count();
    $suspendus = \App\Models\User::where('statut','suspendu')->count();
@endphp
<div class="stats-row" style="margin-bottom:20px;">
    <div class="stat-card" style="--sc-clr:linear-gradient(90deg,#6366f1,#818cf8);">
        <div class="stat-icon" style="background:#ede9fe;"><i class="fas fa-users" style="color:#6366f1;"></i></div>
        <div><div class="stat-value">{{ $total }}</div><div class="stat-label">Total</div></div>
    </div>
    <div class="stat-card" style="--sc-clr:linear-gradient(90deg,#16a34a,#22c55e);">
        <div class="stat-icon" style="background:#f0fdf4;"><i class="fas fa-store" style="color:#16a34a;"></i></div>
        <div><div class="stat-value">{{ $fournisseurs }}</div><div class="stat-label">Fournisseurs</div></div>
    </div>
    <div class="stat-card" style="--sc-clr:linear-gradient(90deg,#2563eb,#60a5fa);">
        <div class="stat-icon" style="background:#dbeafe;"><i class="fas fa-user-friends" style="color:#2563eb;"></i></div>
        <div><div class="stat-value">{{ $acheteurs }}</div><div class="stat-label">Acheteurs</div></div>
    </div>
    <div class="stat-card" style="--sc-clr:linear-gradient(90deg,#ef4444,#f87171);">
        <div class="stat-icon" style="background:#fee2e2;"><i class="fas fa-ban" style="color:#ef4444;"></i></div>
        <div><div class="stat-value">{{ $suspendus }}</div><div class="stat-label">Suspendus</div></div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title"><i class="fas fa-users" style="color:#6366f1;font-size:.85rem;"></i> Liste des membres</span>
    </div>
    <table class="dash-table">
        <thead>
            <tr>
                <th>Utilisateur</th>
                <th>Email</th>
                <th>Rôle</th>
                <th>Statut</th>
                <th>Inscription</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        @foreach($utilisateurs as $u)
        <tr>
            <td>
                <div style="display:flex;align-items:center;gap:10px;">
                    <div style="width:34px;height:34px;border-radius:50%;flex-shrink:0;
                        background:{{ $u->role==='fournisseur'?'#f0fdf4':($u->role==='admin'?'#1e293b':'#ede9fe') }};
                        display:flex;align-items:center;justify-content:center;
                        font-size:.75rem;font-weight:800;
                        color:{{ $u->role==='fournisseur'?'#16a34a':($u->role==='admin'?'#fff':'#6d28d9') }};
                        border:1px solid {{ $u->role==='fournisseur'?'#bbf7d0':($u->role==='admin'?'rgba(255,255,255,.1)':'#e9d5ff') }};">
                        {{ strtoupper(substr($u->prenom ?? 'U', 0, 1)) }}
                    </div>
                    <div>
                        <div style="font-size:.83rem;font-weight:700;color:var(--text);">{{ $u->prenom }} {{ $u->nom }}</div>
                        @if($u->telephone)<div style="font-size:.7rem;color:var(--muted-2);">{{ $u->telephone }}</div>@endif
                    </div>
                </div>
            </td>
            <td style="color:var(--muted);font-size:.79rem;">{{ $u->email }}</td>
            <td>
                @php $rc=['fournisseur'=>'pill-green','acheteur'=>'pill-blue','admin'=>'pill-purple']; @endphp
                <span class="status-pill {{ $rc[$u->role] ?? 'pill-gray' }}">{{ $u->role }}</span>
            </td>
            <td>
                <span class="status-pill {{ $u->statut==='actif'?'pill-green':($u->statut==='suspendu'?'pill-red':'pill-yellow') }}">
                    <span class="pill-dot"></span>{{ $u->statut }}
                </span>
            </td>
            <td style="color:var(--muted);font-size:.74rem;">{{ $u->created_at->format('d/m/Y') }}</td>
            <td>
                @if($u->role !== 'admin')
                    @if($u->statut === 'actif')
                    <form action="{{ route('admin.suspendre', $u) }}" method="POST" style="display:inline;">
                        @csrf
                        <button style="background:none;border:1px solid #fecaca;color:#ef4444;padding:5px 12px;
                            border-radius:8px;font-size:.7rem;cursor:pointer;font-family:'Nunito Sans',sans-serif;
                            font-weight:700;transition:all .2s;"
                            onmouseover="this.style.background='#fef2f2'" onmouseout="this.style.background='none'">
                            <i class="fas fa-ban"></i> Suspendre
                        </button>
                    </form>
                    @else
                    <form action="{{ route('admin.activer', $u) }}" method="POST" style="display:inline;">
                        @csrf
                        <button style="background:none;border:1px solid #bbf7d0;color:#16a34a;padding:5px 12px;
                            border-radius:8px;font-size:.7rem;cursor:pointer;font-family:'Nunito Sans',sans-serif;
                            font-weight:700;transition:all .2s;"
                            onmouseover="this.style.background='var(--green-50)'" onmouseout="this.style.background='none'">
                            <i class="fas fa-check"></i> Activer
                        </button>
                    </form>
                    @endif
                @else
                <span style="color:var(--muted-2);font-size:.73rem;font-style:italic;">Admin</span>
                @endif
            </td>
        </tr>
        @endforeach
        </tbody>
    </table>
    @if($utilisateurs->hasPages())
    <div style="padding:16px 22px;border-top:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;">
        <span style="font-size:.74rem;color:var(--muted);">{{ $utilisateurs->firstItem() }}–{{ $utilisateurs->lastItem() }} sur {{ $utilisateurs->total() }}</span>
        <div style="display:flex;gap:6px;">
            @if($utilisateurs->onFirstPage())
                <span style="padding:6px 12px;border:1px solid var(--border);border-radius:8px;font-size:.75rem;color:var(--muted-2);cursor:default;">←</span>
            @else
                <a href="{{ $utilisateurs->previousPageUrl() }}" style="padding:6px 12px;border:1px solid var(--border);border-radius:8px;font-size:.75rem;color:var(--text);text-decoration:none;transition:all .2s;" onmouseover="this.style.borderColor='var(--green)'" onmouseout="this.style.borderColor='var(--border)'">←</a>
            @endif
            @if($utilisateurs->hasMorePages())
                <a href="{{ $utilisateurs->nextPageUrl() }}" style="padding:6px 12px;border:1px solid var(--border);border-radius:8px;font-size:.75rem;color:var(--text);text-decoration:none;transition:all .2s;" onmouseover="this.style.borderColor='var(--green)'" onmouseout="this.style.borderColor='var(--border)'">→</a>
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
    <div class="activity-title">Résumé des rôles</div>
    @foreach([['fournisseur','#f0fdf4','#16a34a','#bbf7d0'],['acheteur','#dbeafe','#1d4ed8','#bfdbfe'],['admin','#ede9fe','#6d28d9','#e9d5ff']] as $r)
    <div class="activity-item">
        <div class="activity-avatar" style="background:{{ $r[1] }};color:{{ $r[2] }};border-color:{{ $r[3] }};">
            {{ strtoupper(substr($r[0],0,1)) }}
        </div>
        <div class="activity-text">
            <strong>{{ ucfirst($r[0]).'s' }}</strong>
            <div class="activity-time">{{ \App\Models\User::where('role',$r[0])->count() }} membres</div>
        </div>
    </div>
    @endforeach
</div>
@endsection
