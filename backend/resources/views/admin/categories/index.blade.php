@extends('layouts.dashboard')
@section('title','Catégories')
@section('page-title','Catégories')

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
<a href="{{ route('admin.categories.index') }}" class="nav-item active">
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
        <h1>Catégories <span style="color:var(--green);">de produits</span></h1>
        <p style="font-size:.82rem;color:var(--muted);margin-top:4px;">{{ $categories->count() }} catégories configurées.</p>
    </div>
    <a href="{{ route('admin.categories.create') }}"
       style="display:flex;align-items:center;gap:7px;background:var(--green);color:#fff;border:none;
              padding:9px 18px;border-radius:50px;font-size:.8rem;font-weight:700;text-decoration:none;transition:all .2s;"
       onmouseover="this.style.background='var(--green-dark)'" onmouseout="this.style.background='var(--green)'">
        <i class="fas fa-plus"></i> Nouvelle catégorie
    </a>
</div>

@if(session('success'))
    <div style="background:#f0fdf4;border:1px solid #bbf7d0;color:#15803d;border-radius:12px;padding:12px 16px;margin-bottom:16px;font-size:.83rem;font-weight:600;">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div style="background:#fef2f2;border:1px solid #fecaca;color:#dc2626;border-radius:12px;padding:12px 16px;margin-bottom:16px;font-size:.83rem;font-weight:600;">
        <i class="fas fa-times-circle me-2"></i>{{ session('error') }}
    </div>
@endif

<div class="card">
    <table class="dash-table">
        <thead>
            <tr>
                <th>Icône</th>
                <th>Nom</th>
                <th>Description</th>
                <th>Couleur</th>
                <th>Annonces</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        @foreach($categories as $cat)
        <tr>
            <td style="font-size:1.4rem;text-align:center;">{{ $cat->icone }}</td>
            <td style="font-weight:700;">{{ $cat->nom }}</td>
            <td style="font-size:.78rem;color:var(--muted);max-width:200px;">{{ $cat->description ?? '—' }}</td>
            <td>
                <div style="display:flex;align-items:center;gap:8px;">
                    <div style="width:20px;height:20px;border-radius:50%;background:{{ $cat->couleur }};border:1px solid rgba(0,0,0,.1);flex-shrink:0;"></div>
                    <span style="font-size:.74rem;color:var(--muted);font-family:monospace;">{{ $cat->couleur }}</span>
                </div>
            </td>
            <td>
                <span class="status-pill pill-blue">{{ $cat->annonces_count }} annonce(s)</span>
            </td>
            <td>
                <div style="display:flex;gap:6px;align-items:center;">
                    <a href="{{ route('admin.categories.edit', $cat) }}"
                       style="width:28px;height:28px;border-radius:7px;border:1px solid var(--border);background:var(--surface);
                              display:inline-flex;align-items:center;justify-content:center;
                              color:var(--muted);font-size:.75rem;text-decoration:none;transition:all .2s;"
                       onmouseover="this.style.background='#eff6ff';this.style.color='#1d4ed8'"
                       onmouseout="this.style.background='var(--surface)';this.style.color='var(--muted)'">
                        <i class="fas fa-edit"></i>
                    </a>
                    @if($cat->annonces_count == 0)
                    <form action="{{ route('admin.categories.destroy', $cat) }}" method="POST"
                          onsubmit="return confirm('Supprimer « {{ $cat->nom }} » ?')">
                        @csrf @method('DELETE')
                        <button style="width:28px;height:28px;border-radius:7px;border:1px solid #fecaca;background:#fff;
                               display:inline-flex;align-items:center;justify-content:center;
                               color:#ef4444;font-size:.75rem;cursor:pointer;transition:all .2s;"
                               onmouseover="this.style.background='#fef2f2'" onmouseout="this.style.background='#fff'">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                    @else
                    <span style="width:28px;height:28px;border-radius:7px;border:1px solid #f0f0f0;background:#fafafa;
                                 display:inline-flex;align-items:center;justify-content:center;
                                 color:#d1d5db;font-size:.75rem;" title="Impossible de supprimer — contient des annonces">
                        <i class="fas fa-lock"></i>
                    </span>
                    @endif
                </div>
            </td>
        </tr>
        @endforeach
        </tbody>
    </table>
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
        <a href="{{ route('admin.categories.create') }}" class="profile-btn" title="Nouvelle catégorie"><i class="fas fa-plus"></i></a>
    </div>
</div>
@endsection
