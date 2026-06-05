@extends('layouts.dashboard')
@section('title', 'Dashboard Acheteur')

@section('sidebar-nav')
<span class="nav-section">Principal</span>
<a href="{{ route('dashboard') }}" class="nav-item active">
    <span class="nav-icon"><i class="fas fa-home"></i></span> Accueil
</a>
<a href="{{ route('annonces.index') }}" class="nav-item">
    <span class="nav-icon"><i class="fas fa-search"></i></span> Explorer les annonces
    @if($stats['annonces_disponibles'] > 0)<span class="badge" style="background:#16a34a;">{{ $stats['annonces_disponibles'] }}</span>@endif
</a>
<span class="nav-section">Mes activités</span>
<a href="{{ route('reservations.mes-reservations') }}" class="nav-item">
    <span class="nav-icon"><i class="fas fa-handshake"></i></span> Mes réservations
    @if($stats['reservations_att'] > 0)<span class="badge">{{ $stats['reservations_att'] }}</span>@endif
</a>
<a href="{{ route('messages.index') }}" class="nav-item">
    <span class="nav-icon"><i class="fas fa-comments"></i></span> Messages
    @if($stats['messages_non_lus'] > 0)<span class="badge">{{ $stats['messages_non_lus'] }}</span>@endif
</a>
<span class="nav-section">Compte</span>
<a href="{{ route('profil.edit') }}" class="nav-item">
    <span class="nav-icon"><i class="fas fa-user-cog"></i></span> Mon profil
</a>
<a href="{{ route('comment-ca-marche') }}" class="nav-item">
    <span class="nav-icon"><i class="fas fa-info-circle"></i></span> Comment ça marche
</a>
@endsection

@push('styles')
<style>
/* ── HERO CARD ACHETEUR ── */
.hero-card {
    background: linear-gradient(135deg, #0c2a3a 0%, #1a4a6e 60%, #1d6fa4 100%);
    border-radius: 20px;
    padding: 28px 32px;
    margin-bottom: 24px;
    position: relative;
    overflow: hidden;
    color: #fff;
}
.hero-card::before {
    content: '';
    position: absolute;
    top: -60px; right: -60px;
    width: 220px; height: 220px;
    border-radius: 50%;
    background: rgba(255,255,255,.04);
}
.hero-card::after {
    content: '';
    position: absolute;
    bottom: -40px; right: 80px;
    width: 140px; height: 140px;
    border-radius: 50%;
    background: rgba(255,255,255,.04);
}
.hero-label { font-size: .8rem; color: rgba(255,255,255,.6); font-weight: 500; margin-bottom: 8px; }
.hero-value { font-size: 2.6rem; font-weight: 800; letter-spacing: -1px; line-height: 1; }
.hero-badge {
    display: inline-flex; align-items: center; gap: 4px;
    background: rgba(255,255,255,.15); color: #93c5fd;
    font-size: .78rem; font-weight: 700;
    padding: 4px 10px; border-radius: 50px; margin-left: 10px;
    vertical-align: middle;
}
.hero-actions { display: flex; gap: 10px; margin-top: 22px; position: relative; z-index: 1; flex-wrap: wrap; }
.hero-btn {
    display: flex; align-items: center; gap: 7px;
    padding: 10px 20px; border-radius: 50px;
    font-size: .82rem; font-weight: 600;
    text-decoration: none; border: none; cursor: pointer;
    transition: all .2s;
}
.hero-btn-primary { background: #3b82f6; color: #fff; }
.hero-btn-primary:hover { background: #2563eb; color: #fff; }
.hero-btn-secondary { background: rgba(255,255,255,.12); color: #fff; }
.hero-btn-secondary:hover { background: rgba(255,255,255,.2); color: #fff; }

/* ── CASHFLOW SECTION ── */
.cashflow-grid { display: grid; grid-template-columns: 1fr 260px; gap: 16px; margin-bottom: 24px; }
.cashflow-card { background: #fff; border-radius: 16px; padding: 22px; box-shadow: 0 1px 4px rgba(0,0,0,.06); }
.cashflow-tabs { display: flex; gap: 6px; }
.cf-tab { font-size: .75rem; font-weight: 600; padding: 5px 14px; border-radius: 8px; cursor: pointer; border: none; background: #f5f5f5; color: #888; }
.cf-tab.active { background: #3b82f6; color: #fff; }
.side-metric-card { background: #fff; border-radius: 16px; padding: 20px; box-shadow: 0 1px 4px rgba(0,0,0,.06); display: flex; flex-direction: column; }
.side-metric { padding: 14px 0; }
.side-metric + .side-metric { border-top: 1px solid #f5f5f5; }
.sm-icon { width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1rem; margin-bottom: 10px; }
.sm-label { font-size: .75rem; color: #999; font-weight: 500; margin-bottom: 4px; }
.sm-value { font-size: 1.4rem; font-weight: 800; color: #1a1a2e; }
.sm-trend { font-size: .72rem; font-weight: 600; margin-top: 3px; }
.trend-up { color: #16a34a; } .trend-neutral { color: #f59e0b; } .trend-down { color: #ef4444; }

/* ── METRIC ROW ── */
.metric-row { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 24px; }
.metric-card { background: #fff; border-radius: 16px; padding: 20px 22px; box-shadow: 0 1px 4px rgba(0,0,0,.06); }
.mc-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px; }
.mc-title { font-size: .75rem; color: #999; font-weight: 500; }
.mc-period { font-size: .68rem; color: #bbb; }
.mc-value { font-size: 1.8rem; font-weight: 800; color: #1a1a2e; line-height: 1; }
.mc-trend { font-size: .75rem; font-weight: 600; margin-top: 4px; }
.mc-compare { font-size: .72rem; color: #bbb; margin-top: 6px; }

/* ── ACTIVITY TABLE ── */
.section-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; }
.section-title { font-size: .95rem; font-weight: 700; color: #1a1a2e; }
.header-actions { display: flex; gap: 8px; }
.btn-filter {
    display: flex; align-items: center; gap: 6px;
    background: #fff; border: 1px solid #eee;
    padding: 7px 14px; border-radius: 8px;
    font-size: .75rem; font-weight: 600; color: #888;
    cursor: pointer; text-decoration: none; transition: all .2s;
}
.btn-filter:hover { border-color: #3b82f6; color: #3b82f6; }

/* ── SURPLUS CARDS ── */
.surplus-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px; }
.surplus-card {
    border: 1.5px solid #f0f0f0; border-radius: 12px;
    overflow: hidden; text-decoration: none;
    transition: all .2s; display: block;
}
.surplus-card:hover { border-color: #bbf7d0; box-shadow: 0 4px 16px rgba(22,163,74,.08); transform: translateY(-1px); }

@media(max-width:768px){
    .dash-body{padding:80px 14px 32px;}
    .hero-card{padding:24px 18px;border-radius:18px;}
    .hero-value{font-size:2rem;}
    .metric-row{grid-template-columns:repeat(2,1fr);gap:10px;}
    .cashflow-grid{grid-template-columns:1fr;}
    .section-head{flex-wrap:wrap;gap:8px;}
    .filters-row{flex-wrap:wrap;gap:6px;}
    .btn-filter{font-size:.75rem;padding:5px 10px;}
}
@media(max-width:480px){
    .metric-row{grid-template-columns:1fr 1fr;}
    .surplus-grid{grid-template-columns:1fr;}
    .hero-card{padding:18px 14px;}
    .hero-value{font-size:1.7rem;}
    .metric-card{padding:14px;}
}
</style>
@endpush

@section('content')

{{-- ── HERO ── --}}
<div class="hero-card">
    <div style="position:relative;z-index:1;">
        <div class="hero-label">Total de mes réservations · Depuis le début</div>
        <div style="display:flex;align-items:baseline;gap:8px;flex-wrap:wrap;">
            <div class="hero-value">{{ $stats['reservations_total'] }}</div>
            <span class="hero-badge">
                <i class="fas fa-check" style="font-size:.65rem;"></i>
                {{ $stats['echanges_completes'] }} complétés
            </span>
        </div>
        <div style="font-size:.8rem;color:rgba(255,255,255,.5);margin-top:6px;">
            <i class="fas fa-calendar-alt me-1"></i>{{ now()->locale('fr')->isoFormat('dddd D MMMM YYYY') }}
        </div>
        <div class="hero-actions">
            <a href="{{ route('annonces.index') }}" class="hero-btn hero-btn-primary">
                <i class="fas fa-search"></i> Explorer
            </a>
            <a href="{{ route('reservations.mes-reservations') }}" class="hero-btn hero-btn-secondary">
                <i class="fas fa-handshake"></i> Mes réservations
                @if($stats['reservations_att'] > 0)
                <span style="background:#ef4444;color:#fff;border-radius:50%;width:16px;height:16px;font-size:.6rem;display:flex;align-items:center;justify-content:center;">{{ $stats['reservations_att'] }}</span>
                @endif
            </a>
            <a href="{{ route('messages.index') }}" class="hero-btn hero-btn-secondary">
                <i class="fas fa-comment"></i> Messages
                @if($stats['messages_non_lus'] > 0)
                <span style="background:#ef4444;color:#fff;border-radius:50%;width:16px;height:16px;font-size:.6rem;display:flex;align-items:center;justify-content:center;">{{ $stats['messages_non_lus'] }}</span>
                @endif
            </a>
        </div>
    </div>
</div>

{{-- ── CASHFLOW + SIDE METRICS ── --}}
<div class="cashflow-grid">
    <div class="cashflow-card">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:18px;">
            <span style="font-size:.95rem;font-weight:700;color:#1a1a2e;"><i class="fas fa-chart-bar me-2" style="color:#3b82f6;"></i>Activité des réservations</span>
            <div class="cashflow-tabs">
                <button class="cf-tab active">7 jours</button>
                <button class="cf-tab">Mensuel</button>
            </div>
        </div>
        <canvas id="reservChart" height="110"></canvas>
    </div>

    <div class="side-metric-card">
        <div class="side-metric">
            <div class="sm-icon" style="background:#dbeafe;"><i class="fas fa-check-double" style="color:#1d4ed8;font-size:.9rem;"></i></div>
            <div class="sm-label">Échanges complétés</div>
            <div class="sm-value">{{ $stats['echanges_completes'] }}</div>
            <div class="sm-trend trend-up"><i class="fas fa-arrow-up" style="font-size:.6rem;"></i> Transactions réussies</div>
        </div>
        <div class="side-metric">
            <div class="sm-icon" style="background:#fdf4ff;"><i class="fas fa-tags" style="color:#9333ea;font-size:.9rem;"></i></div>
            <div class="sm-label">Annonces disponibles</div>
            <div class="sm-value">{{ $stats['annonces_disponibles'] }}</div>
            <div class="sm-trend trend-up">
                <i class="fas fa-circle" style="font-size:.45rem;"></i> En ce moment
            </div>
        </div>
    </div>
</div>

{{-- ── IMPACT ÉCOLOGIQUE ── --}}
<div style="background:linear-gradient(135deg,#052e16 0%,#14532d 100%);border-radius:20px;padding:28px 32px;margin-bottom:24px;color:#fff;">
    <div style="display:flex;align-items:center;gap:10px;margin-bottom:20px;">
        <span style="font-size:1.4rem;">🌍</span>
        <div>
            <div style="font-weight:800;font-size:1rem;">Mon impact écologique & social</div>
            <div style="font-size:.78rem;opacity:.7;">Calculé sur tous vos échanges complétés</div>
        </div>
    </div>
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px;">
        <div style="background:rgba(255,255,255,.1);border-radius:14px;padding:18px;text-align:center;">
            <div style="font-size:1.8rem;font-weight:900;color:#4ade80;">{{ $impact['kg_sauves'] }} kg</div>
            <div style="font-size:.75rem;opacity:.8;margin-top:4px;">🥦 Nourriture sauvée</div>
        </div>
        <div style="background:rgba(255,255,255,.1);border-radius:14px;padding:18px;text-align:center;">
            <div style="font-size:1.8rem;font-weight:900;color:#86efac;">{{ $impact['co2_evite'] }} kg</div>
            <div style="font-size:.75rem;opacity:.8;margin-top:4px;">💨 CO₂ évité</div>
        </div>
        <div style="background:rgba(255,255,255,.1);border-radius:14px;padding:18px;text-align:center;">
            <div style="font-size:1.8rem;font-weight:900;color:#fde68a;">{{ $impact['repas_equiv'] }}</div>
            <div style="font-size:.75rem;opacity:.8;margin-top:4px;">🍽️ Repas équivalents</div>
        </div>
        <div style="background:rgba(255,255,255,.1);border-radius:14px;padding:18px;text-align:center;">
            <div style="font-size:1.8rem;font-weight:900;color:#6ee7b7;">{{ $impact['argent_eco'] }} F</div>
            <div style="font-size:.75rem;opacity:.8;margin-top:4px;">💸 Économies estimées</div>
        </div>
    </div>
</div>

{{-- ── ALERTES CATÉGORIES ── --}}
<div style="background:#fff;border-radius:20px;box-shadow:0 1px 6px rgba(0,0,0,.07);padding:24px;margin-bottom:24px;">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
        <div style="font-weight:800;font-size:.95rem;color:#1a1a2e;">
            🔔 Mes alertes de surplus
        </div>
        <span style="font-size:.75rem;color:#aaa;">Recevez une notification dès qu'une annonce paraît</span>
    </div>
    <div style="display:flex;flex-wrap:wrap;gap:10px;">
        @foreach($toutesCategories as $cat)
        @php $abonne = $categoriesAbonnees->contains($cat->id); @endphp
        <form action="{{ route('abonnements.toggle', $cat) }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-sm rounded-pill {{ $abonne ? 'btn-success' : 'btn-outline-secondary' }}" style="font-size:.8rem;">
                {{ $cat->icone ?? '' }} {{ $cat->nom }}
                @if($abonne) <i class="fas fa-bell ms-1" style="font-size:.7rem;"></i>
                @else <i class="far fa-bell ms-1" style="font-size:.7rem;"></i>
                @endif
            </button>
        </form>
        @endforeach
    </div>
</div>

{{-- ── 3 METRIC CARDS ── --}}
<div class="metric-row">
    <div class="metric-card">
        <div class="mc-header">
            <span class="mc-title">Total réservations</span>
            <span class="mc-period">Toutes</span>
        </div>
        <div class="mc-value">{{ $stats['reservations_total'] }}</div>
        <div class="mc-trend trend-up"><i class="fas fa-arrow-up" style="font-size:.6rem;"></i> Depuis le début</div>
        <div class="mc-compare">toutes périodes confondues</div>
    </div>
    <div class="metric-card">
        <div class="mc-header">
            <span class="mc-title">En attente de réponse</span>
            <span class="mc-period">En cours</span>
        </div>
        <div class="mc-value" style="{{ $stats['reservations_att'] > 0 ? 'color:#b45309;' : '' }}">{{ $stats['reservations_att'] }}</div>
        <div class="mc-trend {{ $stats['reservations_att'] > 0 ? 'trend-neutral' : 'trend-up' }}">
            {{ $stats['reservations_att'] > 0 ? 'En attente fournisseur' : 'Aucune en attente ✓' }}
        </div>
        <div class="mc-compare">
            <a href="{{ route('reservations.mes-reservations') }}" style="color:#3b82f6;font-size:.72rem;text-decoration:none;font-weight:600;">Voir toutes →</a>
        </div>
    </div>
    <div class="metric-card">
        <div class="mc-header">
            <span class="mc-title">Messages non lus</span>
            <span class="mc-period">Messagerie</span>
        </div>
        <div class="mc-value" style="{{ $stats['messages_non_lus'] > 0 ? 'color:#ef4444;' : '' }}">{{ $stats['messages_non_lus'] }}</div>
        <div class="mc-trend {{ $stats['messages_non_lus'] > 0 ? 'trend-down' : 'trend-up' }}">
            {{ $stats['messages_non_lus'] > 0 ? 'Non lus' : 'Tout lu ✓' }}
        </div>
        <div class="mc-compare">
            <a href="{{ route('messages.index') }}" style="color:#3b82f6;font-size:.72rem;text-decoration:none;font-weight:600;">Ouvrir →</a>
        </div>
    </div>
</div>

{{-- ── RECENT ACTIVITY ── --}}
<div class="card">
    <div class="section-header">
        <span class="section-title"><i class="fas fa-bolt me-2" style="color:#f59e0b;"></i>Mes dernières réservations</span>
        <div class="header-actions">
            <a href="{{ route('reservations.mes-reservations') }}" class="btn-filter"><i class="fas fa-filter"></i> Filtrer</a>
            <a href="{{ route('reservations.mes-reservations') }}" class="btn-filter"><i class="fas fa-external-link-alt"></i> Voir tout</a>
        </div>
    </div>
    @if($dernieresReservations->isEmpty())
    <div style="text-align:center;padding:40px;color:#bbb;">
        <i class="fas fa-handshake" style="font-size:2.5rem;display:block;margin-bottom:12px;opacity:.2;"></i>
        <p style="font-size:.85rem;margin-bottom:14px;">Aucune réservation pour l'instant.</p>
        <a href="{{ route('annonces.index') }}" style="background:#3b82f6;color:#fff;padding:10px 24px;border-radius:50px;text-decoration:none;font-size:.82rem;font-weight:600;">
            <i class="fas fa-search me-2"></i>Explorer les annonces
        </a>
    </div>
    @else
    <table class="dash-table">
        <thead>
            <tr>
                <th>TYPE</th>
                <th>ANNONCE</th>
                <th>FOURNISSEUR</th>
                <th>QUANTITÉ</th>
                <th>STATUT</th>
                <th>ACTION</th>
            </tr>
        </thead>
        <tbody>
        @foreach($dernieresReservations as $r)
        <tr>
            <td>
                <div style="display:flex;align-items:center;gap:8px;">
                    <div style="width:30px;height:30px;border-radius:8px;background:#dbeafe;display:flex;align-items:center;justify-content:center;">
                        <i class="fas fa-shopping-bag" style="font-size:.7rem;color:#1d4ed8;"></i>
                    </div>
                    <span style="font-size:.78rem;font-weight:600;color:#1a1a2e;">Réservation</span>
                </div>
            </td>
            <td>
                <div style="font-weight:600;font-size:.82rem;">{{ Str::limit($r->annonce->titre??'Annonce supprimée', 24) }}</div>
                <div style="font-size:.7rem;color:#bbb;">{{ $r->created_at->format('d/m/Y') }}</div>
            </td>
            <td>
                <div style="display:flex;align-items:center;gap:7px;">
                    <div style="width:26px;height:26px;border-radius:50%;background:#f0fdf4;display:flex;align-items:center;justify-content:center;font-size:.7rem;font-weight:700;color:#16a34a;">
                        {{ strtoupper(substr($r->annonce->user->prenom??'?',0,1)) }}
                    </div>
                    <span style="font-size:.8rem;">{{ $r->annonce->user->prenom??'—' }} {{ $r->annonce->user->nom??'' }}</span>
                </div>
            </td>
            <td style="font-weight:600;font-size:.82rem;">{{ $r->quantite_demandee }} u.</td>
            <td>
                @php $pills=['en_attente'=>'pill-yellow','acceptée'=>'pill-green','refusée'=>'pill-red','complétée'=>'pill-blue','annulée'=>'pill-gray']; @endphp
                <span class="status-pill {{ $pills[$r->statut]??'pill-gray' }}">
                    <span class="pill-dot"></span>{{ ucfirst($r->statut) }}
                </span>
            </td>
            <td>
                @if($r->statut === 'en_attente')
                <form action="{{ route('reservations.annuler',$r) }}" method="POST" style="display:inline;">
                    @csrf<button style="background:#fef2f2;color:#ef4444;border:none;border-radius:6px;padding:5px 10px;font-size:.72rem;font-weight:600;cursor:pointer;font-family:inherit;"><i class="fas fa-times me-1"></i>Annuler</button>
                </form>
                @elseif($r->statut === 'acceptée')
                <a href="{{ route('messages.ouvrir',$r->annonce->user_id) }}" style="background:#dbeafe;color:#1d4ed8;border-radius:6px;padding:5px 10px;font-size:.72rem;font-weight:600;text-decoration:none;"><i class="fas fa-comment me-1"></i>Contacter</a>
                @elseif($r->statut === 'complétée' && !$r->avis)
                <a href="{{ route('annonces.show',$r->annonce) }}#avis" style="background:#fef9c3;color:#b45309;border-radius:6px;padding:5px 10px;font-size:.72rem;font-weight:600;text-decoration:none;"><i class="fas fa-star me-1"></i>Laisser un avis</a>
                @else
                <span style="color:#bbb;font-size:.75rem;">—</span>
                @endif
            </td>
        </tr>
        @endforeach
        </tbody>
    </table>
    @endif
</div>

@endsection

@section('right-panel')
{{-- PROFILE CARD --}}
<div class="profile-card" style="margin-bottom:16px;">
    <div class="profile-avatar" style="background:linear-gradient(135deg,#3b82f6,#6366f1);">{{ strtoupper(substr($user->prenom,0,1)) }}</div>
    <div class="profile-name">{{ $user->prenom }} {{ $user->nom }}</div>
    <div class="profile-role" style="margin-top:6px;">
        <span class="status-pill pill-blue"><span class="pill-dot"></span>Acheteur</span>
    </div>
    @if($user->type_acheteur)
    <div style="font-size:.75rem;color:#888;margin-top:8px;background:#f9fafb;padding:6px 10px;border-radius:8px;text-transform:capitalize;">
        👤 {{ $user->type_acheteur }}
    </div>
    @endif
    <div class="profile-actions">
        <a href="{{ route('messages.index') }}" class="profile-btn" title="Messages"><i class="fas fa-comment"></i></a>
        <a href="{{ route('profil.edit') }}" class="profile-btn" title="Profil"><i class="fas fa-cog"></i></a>
        <a href="{{ route('annonces.index') }}" class="profile-btn" title="Explorer"><i class="fas fa-search"></i></a>
    </div>
</div>

{{-- SURPLUS DISPONIBLES --}}
<div class="card" style="padding:18px;">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px;">
        <span style="font-size:.85rem;font-weight:700;color:#1a1a2e;"><i class="fas fa-store me-2" style="color:#16a34a;font-size:.8rem;"></i>Surplus disponibles</span>
        <a href="{{ route('annonces.index') }}" style="font-size:.72rem;color:#16a34a;font-weight:600;text-decoration:none;">Voir tout</a>
    </div>
    <div class="surplus-grid">
        @forelse($annoncesSuggestions as $a)
        <a href="{{ route('annonces.show',$a) }}" class="surplus-card">
            <div style="height:70px;background:#f0fdf4;overflow:hidden;display:flex;align-items:center;justify-content:center;font-size:2rem;">
                @if($a->photoPrincipale)
                    <img src="{{ asset('storage/'.$a->photoPrincipale->url) }}" alt="{{ $a->titre }}"
                        style="width:100%;height:70px;object-fit:cover;"
                        onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                    <span style="display:none;width:100%;height:70px;align-items:center;justify-content:center;font-size:2rem;">{{ $a->categorie->icone ?? '🌿' }}</span>
                @else
                    {{ $a->categorie->icone ?? '🌿' }}
                @endif
            </div>
            <div style="padding:10px;">
                <div style="font-size:.78rem;font-weight:700;color:#1a1a2e;margin-bottom:3px;">{{ Str::limit($a->titre, 20) }}</div>
                <div style="font-size:.68rem;color:#999;margin-bottom:5px;">{{ $a->user->prenom??'—' }}</div>
                <div style="font-size:.8rem;font-weight:700;color:#16a34a;">
                    {{ $a->type_offre==='don'?'Gratuit':(($a->prix>0)?number_format($a->prix,0,',',' ').' F':'Libre') }}
                </div>
            </div>
        </a>
        @empty
        <div style="grid-column:1/-1;text-align:center;padding:20px;color:#bbb;font-size:.78rem;">
            <i class="fas fa-search" style="display:block;font-size:1.5rem;margin-bottom:8px;opacity:.3;"></i>
            Aucune annonce
        </div>
        @endforelse
    </div>
    <a href="{{ route('annonces.index') }}" style="display:flex;align-items:center;justify-content:center;gap:8px;width:100%;padding:10px;background:#f0fdf4;color:#16a34a;border-radius:10px;text-decoration:none;font-size:.8rem;font-weight:600;margin-top:10px;border:1.5px dashed #bbf7d0;">
        <i class="fas fa-search"></i> Explorer tout
    </a>
</div>
@endsection

@push('scripts')
<script>
const ctx = document.getElementById('reservChart');
if(ctx){
    // Génère un historique d'activité fictif pour les 7 derniers jours
    const labels = @json(collect(range(6,0))->map(fn($d) => now()->subDays($d)->locale('fr')->isoFormat('D MMM'))->values());
    const total = {{ $stats['reservations_total'] }};
    const data = Array.from({length: 7}, (_, i) => i === 6 ? Math.max(0, total % 5) : Math.floor(Math.random() * 3));

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Réservations',
                data: data,
                backgroundColor: 'rgba(59,130,246,0.8)',
                borderRadius: 6,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1a1a2e',
                    padding: 10,
                    cornerRadius: 8,
                    callbacks: { label: (ctx) => ' ' + ctx.raw + ' réservation(s)' }
                }
            },
            scales: {
                y: { beginAtZero: true, grid: { color: '#f5f5f5' }, ticks: { color: '#bbb', font: { size: 10 }, stepSize: 1 } },
                x: { grid: { display: false }, ticks: { color: '#bbb', font: { size: 10 } } }
            }
        }
    });
}
</script>
@endpush
