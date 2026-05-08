@extends('layouts.dashboard')
@section('title','Dashboard Administrateur')
@section('page-title','Tableau de bord')

@section('sidebar-nav')
<span class="nav-section">Vue globale</span>
<a href="{{ route('admin.dashboard') }}" class="nav-item active">
    <span class="nav-icon"><i class="fas fa-th-large"></i></span> Tableau de bord
</a>
<span class="nav-section">Gestion</span>
<a href="{{ route('admin.utilisateurs') }}" class="nav-item">
    <span class="nav-icon"><i class="fas fa-users"></i></span> Utilisateurs
    <span class="nav-badge" style="background:#6366f1;">{{ $stats['utilisateurs'] }}</span>
</a>
<a href="{{ route('admin.annonces') }}" class="nav-item">
    <span class="nav-icon"><i class="fas fa-bullhorn"></i></span> Annonces
    <span class="nav-badge" style="background:#16a34a;">{{ $stats['annonces'] }}</span>
</a>
<a href="{{ route('admin.signalements') }}" class="nav-item">
    <span class="nav-icon"><i class="fas fa-flag"></i></span> Signalements
    @if($stats['signalements'] > 0)
    <span class="nav-badge" style="background:#ef4444;">{{ $stats['signalements'] }}</span>
    @endif
</a>
<span class="nav-section">Plateforme</span>
<a href="{{ route('annonces.index') }}" class="nav-item">
    <span class="nav-icon"><i class="fas fa-globe"></i></span> Voir le site
</a>
@endsection

@section('content')

{{-- HEADER --}}
<div class="page-header">
    <div>
        <h1>Tableau de bord <span style="color:var(--green);">Admin</span> 🛡️</h1>
        <p style="font-size:.82rem;color:var(--muted);margin-top:4px;">Supervision globale de la plateforme AntiGaspiCI.</p>
    </div>
    <div class="date-badge">
        <i class="fas fa-calendar-alt" style="color:var(--green);"></i>
        {{ now()->locale('fr')->isoFormat('dddd D MMMM YYYY') }}
    </div>
</div>

{{-- STAT CARDS --}}
<div class="stats-row">
    <div class="stat-card" style="--sc-clr:linear-gradient(90deg,#6366f1,#818cf8);">
        <div class="stat-icon" style="background:#ede9fe;"><i class="fas fa-users" style="color:#6366f1;"></i></div>
        <div>
            <div class="stat-value">{{ $stats['utilisateurs'] }}</div>
            <div class="stat-label">Utilisateurs</div>
            <div class="stat-trend up"><i class="fas fa-arrow-up"></i> Total inscrits</div>
        </div>
    </div>
    <div class="stat-card" style="--sc-clr:linear-gradient(90deg,#16a34a,#22c55e);">
        <div class="stat-icon" style="background:#f0fdf4;"><i class="fas fa-store" style="color:#16a34a;"></i></div>
        <div>
            <div class="stat-value">{{ $stats['fournisseurs'] }}</div>
            <div class="stat-label">Fournisseurs</div>
            <div class="stat-trend up"><i class="fas fa-circle" style="font-size:.4rem;"></i> Actifs</div>
        </div>
    </div>
    <div class="stat-card" style="--sc-clr:linear-gradient(90deg,#2563eb,#60a5fa);">
        <div class="stat-icon" style="background:#dbeafe;"><i class="fas fa-user-friends" style="color:#2563eb;"></i></div>
        <div>
            <div class="stat-value">{{ $stats['acheteurs'] }}</div>
            <div class="stat-label">Acheteurs</div>
            <div class="stat-trend up"><i class="fas fa-arrow-up"></i> Inscrits</div>
        </div>
    </div>
    <div class="stat-card" style="--sc-clr:linear-gradient(90deg,#f97316,#fb923c);">
        <div class="stat-icon" style="background:#fff7ed;"><i class="fas fa-bullhorn" style="color:#f97316;"></i></div>
        <div>
            <div class="stat-value">{{ $stats['annonces'] }}</div>
            <div class="stat-label">Annonces</div>
            <div class="stat-trend up"><i class="fas fa-arrow-up"></i> Publiées</div>
        </div>
    </div>
    <div class="stat-card" style="--sc-clr:linear-gradient(90deg,#f59e0b,#fbbf24);">
        <div class="stat-icon" style="background:#fef9c3;"><i class="fas fa-exchange-alt" style="color:#d97706;"></i></div>
        <div>
            <div class="stat-value">{{ $stats['reservations'] }}</div>
            <div class="stat-label">Réservations</div>
            <div class="stat-trend neutral">Total</div>
        </div>
    </div>
    <div class="stat-card" style="--sc-clr:linear-gradient(90deg,#ef4444,#f87171);">
        <div class="stat-icon" style="background:#fee2e2;"><i class="fas fa-flag" style="color:#ef4444;"></i></div>
        <div>
            <div class="stat-value">{{ $stats['signalements'] }}</div>
            <div class="stat-label">Signalements</div>
            <div class="stat-trend {{ $stats['signalements'] > 0 ? 'down' : 'up' }}">
                @if($stats['signalements'] > 0)
                <i class="fas fa-exclamation-triangle"></i> À traiter
                @else
                <i class="fas fa-check"></i> RAS
                @endif
            </div>
        </div>
    </div>
</div>

{{-- CHART + SIGNALEMENTS --}}
<div style="display:grid;grid-template-columns:1.6fr 1fr;gap:20px;margin-bottom:20px;">

    {{-- Chart --}}
    <div class="card" style="margin-bottom:0;">
        <div class="chart-header">
            <span class="card-title">
                <i class="fas fa-chart-bar" style="color:#6366f1;font-size:.9rem;"></i>
                Vue d'ensemble
            </span>
            <span class="chart-period"><i class="fas fa-calendar-alt" style="color:var(--green);margin-right:4px;"></i>Toutes périodes</span>
        </div>
        <canvas id="adminChart" height="100"></canvas>
    </div>

    {{-- Signalements --}}
    <div class="card" style="margin-bottom:0;">
        <div class="card-header">
            <span class="card-title">
                <i class="fas fa-flag" style="color:#ef4444;font-size:.85rem;"></i>
                Signalements urgents
            </span>
            <a href="{{ route('admin.signalements') }}" class="card-action">Voir tout</a>
        </div>
        @forelse($signalements->take(4) as $sig)
        <div style="padding:10px 0;border-bottom:1px solid #f1f5f9;">
            <div style="display:flex;justify-content:space-between;align-items:start;gap:8px;">
                <div>
                    <div style="font-size:.8rem;font-weight:700;color:var(--text);margin-bottom:2px;">
                        {{ Str::limit($sig->raison, 30) }}
                    </div>
                    <div style="font-size:.7rem;color:var(--muted);">
                        {{ $sig->auteur->prenom ?? '—' }} {{ $sig->auteur->nom ?? '' }} · {{ $sig->created_at->diffForHumans() }}
                    </div>
                </div>
                <form action="{{ route('admin.traiter-signalement', $sig) }}" method="POST">
                    @csrf
                    <button style="background:var(--green-50);border:1px solid var(--green-100);color:var(--green);
                        padding:5px 11px;border-radius:8px;font-size:.68rem;cursor:pointer;
                        font-family:'Nunito Sans',sans-serif;font-weight:700;white-space:nowrap;transition:all .2s;"
                        onmouseover="this.style.background='var(--green-100)'" onmouseout="this.style.background='var(--green-50)'">
                        <i class="fas fa-check"></i> Traité
                    </button>
                </form>
            </div>
        </div>
        @empty
        <div style="text-align:center;padding:28px 0;color:var(--muted-2);">
            <i class="fas fa-check-circle" style="font-size:2rem;color:var(--green);opacity:.5;display:block;margin-bottom:8px;"></i>
            <div style="font-size:.8rem;font-weight:600;">Aucun signalement en attente</div>
        </div>
        @endforelse
    </div>
</div>

{{-- DERNIERS INSCRITS --}}
<div class="card">
    <div class="card-header">
        <span class="card-title">
            <i class="fas fa-user-plus" style="color:#6366f1;font-size:.85rem;"></i>
            Derniers inscrits
        </span>
        <a href="{{ route('admin.utilisateurs') }}" class="card-action">Gérer tout</a>
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
        @foreach($derniersUtilisateurs as $u)
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
                        <button style="background:none;border:1px solid #fecaca;color:#ef4444;padding:5px 11px;
                            border-radius:8px;font-size:.7rem;cursor:pointer;font-family:'Nunito Sans',sans-serif;
                            font-weight:700;transition:all .2s;"
                            onmouseover="this.style.background='#fef2f2'" onmouseout="this.style.background='none'">
                            <i class="fas fa-ban"></i> Suspendre
                        </button>
                    </form>
                    @else
                    <form action="{{ route('admin.activer', $u) }}" method="POST" style="display:inline;">
                        @csrf
                        <button style="background:none;border:1px solid #bbf7d0;color:#16a34a;padding:5px 11px;
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
</div>

{{-- DERNIÈRES ANNONCES --}}
<div class="card">
    <div class="card-header">
        <span class="card-title">
            <i class="fas fa-bullhorn" style="color:#f97316;font-size:.85rem;"></i>
            Dernières annonces publiées
        </span>
        <a href="{{ route('admin.annonces') }}" class="card-action">Tout gérer</a>
    </div>
    <table class="dash-table">
        <thead>
            <tr><th>Titre</th><th>Fournisseur</th><th>Type</th><th>Statut</th><th>Date</th><th>Actions</th></tr>
        </thead>
        <tbody>
        @forelse($dernieresAnnonces as $a)
        <tr>
            <td style="font-weight:700;max-width:200px;">{{ Str::limit($a->titre, 34) }}</td>
            <td style="color:var(--muted);font-size:.79rem;">{{ $a->user->prenom ?? '—' }} {{ $a->user->nom ?? '' }}</td>
            <td>
                @php $tc=['vente'=>'pill-blue','don'=>'pill-green','alimentation_animale'=>'pill-yellow','transformation'=>'pill-purple']; @endphp
                <span class="status-pill {{ $tc[$a->type_offre] ?? 'pill-gray' }}">{{ $a->type_offre }}</span>
            </td>
            <td>
                @php $sc=['disponible'=>'pill-green','reservé'=>'pill-yellow','expiré'=>'pill-gray','supprimé'=>'pill-red']; @endphp
                <span class="status-pill {{ $sc[$a->statut] ?? 'pill-gray' }}">
                    <span class="pill-dot"></span>{{ $a->statut }}
                </span>
            </td>
            <td style="color:var(--muted);font-size:.73rem;">{{ $a->created_at->format('d/m/Y') }}</td>
            <td>
                <div style="display:flex;gap:6px;align-items:center;">
                    <a href="{{ route('annonces.show', $a) }}" style="width:28px;height:28px;border-radius:7px;
                        border:1px solid var(--border);background:var(--surface);
                        display:inline-flex;align-items:center;justify-content:center;
                        color:var(--muted);font-size:.75rem;text-decoration:none;transition:all .2s;"
                        onmouseover="this.style.background='var(--green-50)';this.style.color='var(--green)'"
                        onmouseout="this.style.background='var(--surface)';this.style.color='var(--muted)'">
                        <i class="fas fa-eye"></i>
                    </a>
                    @if($a->statut !== 'supprimé')
                    <form action="{{ route('admin.supprimer-annonce', $a) }}" method="POST"
                        onsubmit="return confirm('Supprimer cette annonce ?')">
                        @csrf @method('DELETE')
                        <button style="width:28px;height:28px;border-radius:7px;
                            border:1px solid #fecaca;background:#fff;
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
        @empty
        <tr class="empty-row"><td colspan="6"><i class="fas fa-inbox"></i>Aucune annonce</td></tr>
        @endforelse
        </tbody>
    </table>
</div>

@endsection

@section('right-panel')
<div class="profile-card">
    <div class="profile-avatar" style="background:linear-gradient(135deg,#6366f1,#818cf8);">🛡️</div>
    <div class="profile-name">{{ Auth::user()->prenom ?? 'Administrateur' }} {{ Auth::user()->nom ?? '' }}</div>
    <div class="profile-role-badge">Administrateur</div>
    <div class="profile-actions">
        <a href="{{ route('admin.utilisateurs') }}" class="profile-btn" title="Utilisateurs"><i class="fas fa-users"></i></a>
        <a href="{{ route('admin.signalements') }}" class="profile-btn" title="Signalements"><i class="fas fa-flag"></i></a>
        <a href="{{ route('admin.annonces') }}" class="profile-btn" title="Annonces"><i class="fas fa-bullhorn"></i></a>
    </div>
</div>

<div class="activity-card">
    <div class="activity-title">Activité récente</div>
    @foreach($derniersUtilisateurs->take(5) as $u)
    <div class="activity-item">
        <div class="activity-avatar"
            style="background:{{ $u->role==='fournisseur'?'#f0fdf4':($u->role==='admin'?'#ede9fe':'#dbeafe') }};
                color:{{ $u->role==='fournisseur'?'#16a34a':($u->role==='admin'?'#6d28d9':'#1d4ed8') }};
                border-color:{{ $u->role==='fournisseur'?'#bbf7d0':($u->role==='admin'?'#e9d5ff':'#bfdbfe') }};">
            {{ strtoupper(substr($u->prenom ?? 'U', 0, 1)) }}
        </div>
        <div class="activity-text">
            <strong>{{ $u->prenom }} {{ $u->nom }}</strong> s'est inscrit en tant que <strong>{{ $u->role }}</strong>
            <div class="activity-time">{{ $u->created_at->diffForHumans() }}</div>
        </div>
    </div>
    @endforeach

    @if($stats['signalements'] > 0)
    <div style="margin-top:12px;background:#fef2f2;border-radius:10px;padding:12px;border:1px solid #fecaca;">
        <div style="font-size:.74rem;font-weight:800;color:#b91c1c;margin-bottom:5px;">
            <i class="fas fa-exclamation-triangle"></i> {{ $stats['signalements'] }} signalement(s) en attente
        </div>
        <a href="{{ route('admin.signalements') }}"
            style="font-size:.73rem;color:#ef4444;font-weight:700;text-decoration:none;">
            Traiter maintenant →
        </a>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
const ctx = document.getElementById('adminChart');
if(ctx){
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Acheteurs','Fournisseurs','Annonces','Réservations','Signalements'],
            datasets:[{
                label: 'Total',
                data: [{{ $stats['acheteurs'] }},{{ $stats['fournisseurs'] }},{{ $stats['annonces'] }},{{ $stats['reservations'] }},{{ $stats['signalements'] }}],
                backgroundColor: [
                    'rgba(99,102,241,.8)',
                    'rgba(22,163,74,.8)',
                    'rgba(249,115,22,.8)',
                    'rgba(245,158,11,.8)',
                    'rgba(239,68,68,.8)'
                ],
                borderRadius: 10,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#0f172a',
                    titleFont: { family: 'Rubik', weight: '700' },
                    bodyFont: { family: 'Nunito Sans' },
                    padding: 12, cornerRadius: 10,
                    displayColors: true,
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: '#f1f5f9', drawBorder: false },
                    ticks: { color: '#94a3b8', font: { size: 10 }, stepSize: 1 }
                },
                x: {
                    grid: { display: false },
                    ticks: { color: '#64748b', font: { size: 11, weight: '600' } }
                }
            }
        }
    });
}
</script>
@endpush
