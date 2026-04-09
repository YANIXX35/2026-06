@extends('layouts.dashboard')
@section('title', 'Dashboard Administrateur')

@section('sidebar-nav')
<span class="nav-section">Vue globale</span>
<a href="{{ route('admin.dashboard') }}" class="nav-item active">
    <span class="nav-icon"><i class="fas fa-tachometer-alt"></i></span> Tableau de bord
</a>
<span class="nav-section">Gestion</span>
<a href="{{ route('admin.utilisateurs') }}" class="nav-item">
    <span class="nav-icon"><i class="fas fa-users"></i></span> Utilisateurs
    <span class="badge" style="background:#6366f1;">{{ $stats['utilisateurs'] }}</span>
</a>
<a href="{{ route('admin.annonces') }}" class="nav-item">
    <span class="nav-icon"><i class="fas fa-bullhorn"></i></span> Annonces
    <span class="badge" style="background:#16a34a;">{{ $stats['annonces'] }}</span>
</a>
<a href="{{ route('admin.signalements') }}" class="nav-item">
    <span class="nav-icon"><i class="fas fa-flag"></i></span> Signalements
    @if($stats['signalements'] > 0)<span class="badge">{{ $stats['signalements'] }}</span>@endif
</a>
<span class="nav-section">Plateforme</span>
<a href="{{ route('annonces.index') }}" class="nav-item">
    <span class="nav-icon"><i class="fas fa-globe"></i></span> Voir le site
</a>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1>Tableau de bord Admin 🛡️</h1>
        <p style="font-size:.85rem;color:#888;margin-top:4px;">Supervision globale de la plateforme AntiGaspiCI.</p>
    </div>
    <div class="date-badge">
        <i class="fas fa-calendar-alt" style="color:#16a34a;"></i>
        {{ now()->locale('fr')->isoFormat('dddd D MMMM YYYY') }}
    </div>
</div>

<div class="stats-row">
    <div class="stat-card">
        <div class="stat-icon" style="background:#ede9fe;"><i class="fas fa-users" style="color:#7c3aed;"></i></div>
        <div>
            <div class="stat-value">{{ $stats['utilisateurs'] }}</div>
            <div class="stat-label">Utilisateurs</div>
            <div class="stat-trend up">Total inscrits</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#f0fdf4;"><i class="fas fa-store" style="color:#16a34a;"></i></div>
        <div>
            <div class="stat-value">{{ $stats['fournisseurs'] }}</div>
            <div class="stat-label">Fournisseurs</div>
            <div class="stat-trend up"><i class="fas fa-circle" style="font-size:.5rem;"></i> Actifs</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#dbeafe;"><i class="fas fa-user-friends" style="color:#1d4ed8;"></i></div>
        <div>
            <div class="stat-value">{{ $stats['acheteurs'] }}</div>
            <div class="stat-label">Acheteurs</div>
            <div class="stat-trend up">Inscrits</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#fff7ed;"><i class="fas fa-bullhorn" style="color:#ea580c;"></i></div>
        <div>
            <div class="stat-value">{{ $stats['annonces'] }}</div>
            <div class="stat-label">Annonces</div>
            <div class="stat-trend up">Total publiées</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#fef9c3;"><i class="fas fa-exchange-alt" style="color:#b45309;"></i></div>
        <div>
            <div class="stat-value">{{ $stats['reservations'] }}</div>
            <div class="stat-label">Réservations</div>
            <div class="stat-trend up">Total</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#fee2e2;"><i class="fas fa-flag" style="color:#dc2626;"></i></div>
        <div>
            <div class="stat-value">{{ $stats['signalements'] }}</div>
            <div class="stat-label">Signalements</div>
            <div class="stat-trend {{ $stats['signalements']>0?'down':'up' }}">
                {{ $stats['signalements']>0?'À traiter':'OK' }}
            </div>
        </div>
    </div>
</div>

<!-- Chart + Signalements -->
<div style="display:grid;grid-template-columns:1.5fr 1fr;gap:20px;margin-bottom:20px;">
    <div class="card" style="margin-bottom:0;">
        <div class="chart-header">
            <span class="card-title"><i class="fas fa-chart-bar me-2" style="color:#7c3aed;"></i>Répartition utilisateurs</span>
        </div>
        <canvas id="adminChart" height="120"></canvas>
    </div>
    <div class="card" style="margin-bottom:0;">
        <div class="card-header">
            <span class="card-title"><i class="fas fa-flag me-2" style="color:#ef4444;"></i>Signalements urgents</span>
            <a href="{{ route('admin.signalements') }}" class="card-action">Voir tout</a>
        </div>
        @forelse($signalements->take(4) as $sig)
        <div style="padding:9px 0;border-bottom:1px solid #f5f5f5;">
            <div style="display:flex;justify-content:space-between;align-items:start;gap:8px;">
                <div>
                    <div style="font-size:.8rem;font-weight:600;color:#1a1a2e;">{{ Str::limit($sig->raison,28) }}</div>
                    <div style="font-size:.72rem;color:#999;">{{ $sig->auteur->prenom??'—' }} {{ $sig->auteur->nom??'' }} · {{ $sig->created_at->diffForHumans() }}</div>
                </div>
                <form action="{{ route('admin.traiter-signalement',$sig) }}" method="POST">
                    @csrf<button style="background:#f0fdf4;border:1px solid #bbf7d0;color:#16a34a;padding:4px 10px;border-radius:6px;font-size:.7rem;cursor:pointer;font-family:inherit;font-weight:600;white-space:nowrap;">
                        <i class="fas fa-check me-1"></i>Traité
                    </button>
                </form>
            </div>
        </div>
        @empty
        <div style="text-align:center;padding:24px;color:#bbb;font-size:.8rem;">
            <i class="fas fa-check-circle" style="display:block;font-size:1.5rem;margin-bottom:6px;color:#16a34a;opacity:.5;"></i>
            Aucun signalement en attente
        </div>
        @endforelse
    </div>
</div>

<!-- Derniers inscrits -->
<div class="card">
    <div class="card-header">
        <span class="card-title"><i class="fas fa-user-plus me-2" style="color:#7c3aed;"></i>Derniers inscrits</span>
        <a href="{{ route('admin.utilisateurs') }}" class="card-action">Gérer tout</a>
    </div>
    <table class="dash-table">
        <thead><tr><th>Utilisateur</th><th>Email</th><th>Rôle</th><th>Statut</th><th>Inscription</th><th>Actions</th></tr></thead>
        <tbody>
        @foreach($derniersUtilisateurs as $u)
        <tr>
            <td>
                <div style="display:flex;align-items:center;gap:10px;">
                    <div style="width:32px;height:32px;border-radius:50%;background:{{ $u->role==='fournisseur'?'#f0fdf4':'#ede9fe' }};display:flex;align-items:center;justify-content:center;font-size:.75rem;font-weight:700;color:{{ $u->role==='fournisseur'?'#16a34a':'#7c3aed' }};">
                        {{ strtoupper(substr($u->prenom,0,1)) }}
                    </div>
                    <div>
                        <div style="font-size:.82rem;font-weight:600;">{{ $u->prenom }} {{ $u->nom }}</div>
                        @if($u->telephone)<div style="font-size:.7rem;color:#bbb;">{{ $u->telephone }}</div>@endif
                    </div>
                </div>
            </td>
            <td style="color:#888;font-size:.8rem;">{{ $u->email }}</td>
            <td>
                @php $rc=['fournisseur'=>'pill-green','acheteur'=>'pill-blue','admin'=>'pill-red']; @endphp
                <span class="status-pill {{ $rc[$u->role]??'pill-gray' }}">{{ $u->role }}</span>
            </td>
            <td>
                <span class="status-pill {{ $u->statut==='actif'?'pill-green':($u->statut==='suspendu'?'pill-red':'pill-yellow') }}">
                    <span class="pill-dot"></span>{{ $u->statut }}
                </span>
            </td>
            <td style="color:#888;font-size:.75rem;">{{ $u->created_at->format('d/m/Y') }}</td>
            <td>
                @if($u->role !== 'admin')
                    @if($u->statut === 'actif')
                    <form action="{{ route('admin.suspendre',$u) }}" method="POST" style="display:inline;">
                        @csrf<button style="background:none;border:1px solid #fecaca;color:#ef4444;padding:4px 10px;border-radius:6px;font-size:.72rem;cursor:pointer;font-family:inherit;font-weight:600;">
                            <i class="fas fa-ban me-1"></i>Suspendre
                        </button>
                    </form>
                    @else
                    <form action="{{ route('admin.activer',$u) }}" method="POST" style="display:inline;">
                        @csrf<button style="background:none;border:1px solid #bbf7d0;color:#16a34a;padding:4px 10px;border-radius:6px;font-size:.72rem;cursor:pointer;font-family:inherit;font-weight:600;">
                            <i class="fas fa-check me-1"></i>Activer
                        </button>
                    </form>
                    @endif
                @else
                <span style="color:#bbb;font-size:.75rem;">Admin</span>
                @endif
            </td>
        </tr>
        @endforeach
        </tbody>
    </table>
</div>

<!-- Dernières annonces -->
<div class="card">
    <div class="card-header">
        <span class="card-title"><i class="fas fa-bullhorn me-2" style="color:#ea580c;"></i>Dernières annonces publiées</span>
        <a href="{{ route('admin.annonces') }}" class="card-action">Tout gérer</a>
    </div>
    <table class="dash-table">
        <thead><tr><th>Titre</th><th>Fournisseur</th><th>Type</th><th>Statut</th><th>Date</th><th>Action</th></tr></thead>
        <tbody>
        @foreach($dernieresAnnonces as $a)
        <tr>
            <td style="font-weight:600;">{{ Str::limit($a->titre,32) }}</td>
            <td style="color:#888;font-size:.8rem;">{{ $a->user->prenom??'—' }} {{ $a->user->nom??'' }}</td>
            <td>
                @php $tc=['vente'=>'pill-blue','don'=>'pill-green','alimentation_animale'=>'pill-yellow','transformation'=>'pill-gray']; @endphp
                <span class="status-pill {{ $tc[$a->type_offre]??'pill-gray' }}">{{ $a->type_offre }}</span>
            </td>
            <td>
                @php $sc=['disponible'=>'pill-green','reservé'=>'pill-yellow','expiré'=>'pill-gray','supprimé'=>'pill-red']; @endphp
                <span class="status-pill {{ $sc[$a->statut]??'pill-gray' }}"><span class="pill-dot"></span>{{ $a->statut }}</span>
            </td>
            <td style="color:#888;font-size:.75rem;">{{ $a->created_at->format('d/m/Y') }}</td>
            <td>
                <div style="display:flex;gap:8px;align-items:center;">
                    <a href="{{ route('annonces.show',$a) }}" style="color:#888;font-size:.82rem;"><i class="fas fa-eye"></i></a>
                    @if($a->statut !== 'supprimé')
                    <form action="{{ route('admin.supprimer-annonce',$a) }}" method="POST" onsubmit="return confirm('Supprimer ?')">
                        @csrf @method('DELETE')
                        <button style="background:none;border:none;color:#ef4444;font-size:.82rem;cursor:pointer;"><i class="fas fa-trash"></i></button>
                    </form>
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
<div class="profile-card" style="background:linear-gradient(135deg,#1a1a2e,#312e81);color:#fff;">
    <div class="profile-avatar" style="background:rgba(255,255,255,.15);border:2px solid rgba(255,255,255,.3);">🛡️</div>
    <div class="profile-name" style="color:#fff;">Administrateur</div>
    <div class="profile-role" style="margin-top:6px;">
        <span style="background:rgba(255,255,255,.15);color:#fff;font-size:.72rem;padding:3px 12px;border-radius:50px;font-weight:600;">AntiGaspiCI</span>
    </div>
    <div class="profile-actions" style="margin-top:16px;">
        <a href="{{ route('admin.utilisateurs') }}" class="profile-btn" style="border-color:rgba(255,255,255,.2);color:rgba(255,255,255,.7);"><i class="fas fa-users"></i></a>
        <a href="{{ route('admin.signalements') }}" class="profile-btn" style="border-color:rgba(255,255,255,.2);color:rgba(255,255,255,.7);"><i class="fas fa-flag"></i></a>
        <a href="{{ route('admin.annonces') }}" class="profile-btn" style="border-color:rgba(255,255,255,.2);color:rgba(255,255,255,.7);"><i class="fas fa-bullhorn"></i></a>
    </div>
</div>

<div class="activity-card">
    <div class="activity-title">Activité récente</div>
    @foreach($derniersUtilisateurs->take(4) as $u)
    <div class="activity-item">
        <div class="activity-avatar" style="background:{{ $u->role==='fournisseur'?'#f0fdf4':'#ede9fe' }};font-size:.7rem;font-weight:700;color:{{ $u->role==='fournisseur'?'#16a34a':'#7c3aed' }};">
            {{ strtoupper(substr($u->prenom,0,1)) }}
        </div>
        <div class="activity-text">
            <strong>{{ $u->prenom }} {{ $u->nom }}</strong> s'est inscrit en tant que <strong>{{ $u->role }}</strong>
            <div class="activity-time">{{ $u->created_at->diffForHumans() }}</div>
        </div>
    </div>
    @endforeach
    @if($signalements->count() > 0)
    <div style="margin-top:12px;background:#fff5f5;border-radius:10px;padding:12px;">
        <div style="font-size:.75rem;font-weight:700;color:#ef4444;margin-bottom:6px;"><i class="fas fa-exclamation-triangle me-1"></i>{{ $stats['signalements'] }} signalement(s) en attente</div>
        <a href="{{ route('admin.signalements') }}" style="font-size:.75rem;color:#ef4444;font-weight:600;text-decoration:none;">Traiter maintenant →</a>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
const ctx = document.getElementById('adminChart');
if(ctx){
    new Chart(ctx,{
        type:'bar',
        data:{
            labels:['Acheteurs','Fournisseurs','Annonces','Réservations','Signalements'],
            datasets:[{
                label:'Total',
                data:[{{ $stats['acheteurs'] }},{{ $stats['fournisseurs'] }},{{ $stats['annonces'] }},{{ $stats['reservations'] }},{{ $stats['signalements'] }}],
                backgroundColor:['rgba(99,102,241,.7)','rgba(22,163,74,.7)','rgba(234,88,12,.7)','rgba(245,158,11,.7)','rgba(239,68,68,.7)'],
                borderRadius:8,borderSkipped:false,
            }]
        },
        options:{
            responsive:true,
            plugins:{legend:{display:false},tooltip:{backgroundColor:'#1a1a2e',padding:10,cornerRadius:8}},
            scales:{
                y:{beginAtZero:true,grid:{color:'#f5f5f5'},ticks:{color:'#bbb',font:{size:10}}},
                x:{grid:{display:false},ticks:{color:'#888',font:{size:11}}}
            }
        }
    });
}
</script>
@endpush
