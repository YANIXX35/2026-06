@extends('layouts.dashboard')
@section('title', 'Dashboard Fournisseur')

@section('sidebar-nav')
<span class="nav-section">Principal</span>
<a href="{{ route('dashboard') }}" class="nav-item active">
    <span class="nav-icon"><i class="fas fa-home"></i></span> Accueil
</a>
<a href="{{ route('annonces.create') }}" class="nav-item">
    <span class="nav-icon"><i class="fas fa-plus-circle"></i></span> Publier un surplus
</a>
<span class="nav-section">Gestion</span>
<a href="{{ route('annonces.mes-annonces') }}" class="nav-item">
    <span class="nav-icon"><i class="fas fa-bullhorn"></i></span> Mes annonces
    @if($stats['annonces_actives'] > 0)<span class="badge" style="background:#16a34a;">{{ $stats['annonces_actives'] }}</span>@endif
</a>
<a href="{{ route('reservations.mes-reservations') }}" class="nav-item">
    <span class="nav-icon"><i class="fas fa-handshake"></i></span> Réservations
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
<a href="{{ route('annonces.index') }}" class="nav-item">
    <span class="nav-icon"><i class="fas fa-store"></i></span> La boutique
</a>
@endsection

@push('styles')
<style>
/* ── HERO CARD ── */
.hero-card {
    background: linear-gradient(135deg, #0c3a26 0%, #145a38 60%, #1a7a4a 100%);
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
    background: rgba(255,255,255,.15); color: #86efac;
    font-size: .78rem; font-weight: 700;
    padding: 4px 10px; border-radius: 50px; margin-left: 10px;
    vertical-align: middle;
}
.hero-actions { display: flex; gap: 10px; margin-top: 22px; position: relative; z-index: 1; }
.hero-btn {
    display: flex; align-items: center; gap: 7px;
    padding: 10px 20px; border-radius: 50px;
    font-size: .82rem; font-weight: 600;
    text-decoration: none; border: none; cursor: pointer;
    transition: all .2s;
}
.hero-btn-primary { background: #22c55e; color: #fff; }
.hero-btn-primary:hover { background: #16a34a; color: #fff; }
.hero-btn-secondary { background: rgba(255,255,255,.12); color: #fff; }
.hero-btn-secondary:hover { background: rgba(255,255,255,.2); color: #fff; }

/* ── CASHFLOW SECTION ── */
.cashflow-grid { display: grid; grid-template-columns: 1fr 260px; gap: 16px; margin-bottom: 24px; }
.cashflow-card { background: #fff; border-radius: 16px; padding: 22px; box-shadow: 0 1px 4px rgba(0,0,0,.06); }
.cashflow-tabs { display: flex; gap: 6px; }
.cf-tab {
    font-size: .75rem; font-weight: 600; padding: 5px 14px;
    border-radius: 8px; cursor: pointer; border: none; background: #f5f5f5; color: #888;
}
.cf-tab.active { background: #16a34a; color: #fff; }
.side-metric-card { background: #fff; border-radius: 16px; padding: 20px; box-shadow: 0 1px 4px rgba(0,0,0,.06); display: flex; flex-direction: column; gap: 0; }
.side-metric { padding: 14px 0; }
.side-metric + .side-metric { border-top: 1px solid #f5f5f5; }
.sm-icon { width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1rem; margin-bottom: 10px; }
.sm-label { font-size: .75rem; color: #999; font-weight: 500; margin-bottom: 4px; }
.sm-value { font-size: 1.4rem; font-weight: 800; color: #1a1a2e; }
.sm-trend { font-size: .72rem; font-weight: 600; margin-top: 3px; }
.trend-up { color: #16a34a; } .trend-down { color: #ef4444; } .trend-neutral { color: #f59e0b; }

/* ── METRIC ROW ── */
.metric-row { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 24px; }
.metric-card { background: #fff; border-radius: 16px; padding: 20px 22px; box-shadow: 0 1px 4px rgba(0,0,0,.06); }
.mc-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px; }
.mc-title { font-size: .75rem; color: #999; font-weight: 500; }
.mc-period { font-size: .68rem; color: #bbb; }
.mc-value { font-size: 1.8rem; font-weight: 800; color: #1a1a2e; line-height: 1; }
.mc-trend { font-size: .75rem; font-weight: 600; margin-top: 4px; }
.mc-compare { font-size: .72rem; color: #bbb; margin-top: 6px; }

/* ── ACTIVITY TABLE HEADER ── */
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
.btn-filter:hover { border-color: #16a34a; color: #16a34a; }

/* ── ANNONCE CARD ── */
.annonce-list-card { border: 1.5px solid #f0f0f0; border-radius: 12px; padding: 14px 16px; margin-bottom: 10px; transition: all .2s; }
.annonce-list-card:hover { border-color: #bbf7d0; box-shadow: 0 2px 12px rgba(22,163,74,.08); }
.alc-bar { height: 4px; background: #f0f0f0; border-radius: 4px; margin-top: 10px; overflow: hidden; }
.alc-fill { height: 100%; background: linear-gradient(90deg, #16a34a, #22c55e); border-radius: 4px; }
</style>
@endpush

@section('content')

{{-- ── HERO ── --}}
<div class="hero-card">
    <div style="position:relative;z-index:1;">
        <div class="hero-label">Total des vues · Toutes mes annonces</div>
        <div style="display:flex;align-items:baseline;gap:8px;flex-wrap:wrap;">
            <div class="hero-value">{{ number_format($stats['total_vues'], 0, ',', ' ') }}</div>
            <span class="hero-badge"><i class="fas fa-arrow-up" style="font-size:.65rem;"></i> vues cumulées</span>
        </div>
        <div style="font-size:.8rem;color:rgba(255,255,255,.5);margin-top:6px;">
            <i class="fas fa-calendar-alt me-1"></i>{{ now()->locale('fr')->isoFormat('dddd D MMMM YYYY') }}
        </div>
        <div class="hero-actions">
            <a href="{{ route('annonces.create') }}" class="hero-btn hero-btn-primary">
                <i class="fas fa-plus"></i> Publier
            </a>
            <a href="{{ route('annonces.mes-annonces') }}" class="hero-btn hero-btn-secondary">
                <i class="fas fa-list"></i> Mes annonces
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

{{-- ── TABLEAU DE BORD D'IMPACT ÉCOLOGIQUE & BADGES (GAMIFICATION) ── --}}
<div style="background: linear-gradient(135deg, #052e16 0%, #0d3d1f 100%); border-radius: 20px; padding: 24px; color: #fff; margin-bottom: 24px; box-shadow: 0 10px 25px rgba(5,46,22,0.15);">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; margin-bottom: 20px; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 14px;">
        <div>
            <h4 style="font-family:'Rubik',sans-serif; font-size: 1.15rem; font-weight: 800; margin: 0; color: #4ade80;">
                🌱 Mon Impact Écologique & Économique
            </h4>
            <p style="font-size: .8rem; color: rgba(255,255,255,0.7); margin: 2px 0 0 0;">Vos actions concrètes pour préserver la planète et éviter le gaspillage.</p>
        </div>
        <span style="background: rgba(74,222,128,0.15); color: #4ade80; border: 1px solid rgba(74,222,128,0.3); font-size: .75rem; font-weight: 700; padding: 4px 12px; border-radius: 50px;">
            <i class="fas fa-medal me-1"></i> {{ count(array_filter(Auth::user()->badges, fn($b) => $b['debloque'])) }}/{{ count(Auth::user()->badges) }} Badges Débloqués
        </span>
    </div>

    <!-- Impact Stats Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 24px;">
        <!-- Card 1 -->
        <div style="background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.12); border-radius: 14px; padding: 16px; display: flex; align-items: center; gap: 14px;">
            <div style="width: 46px; height: 46px; border-radius: 12px; background: rgba(34,197,94,0.2); color: #4ade80; display: flex; align-items: center; justify-content: center; font-size: 1.3rem;">
                🥗
            </div>
            <div>
                <div style="font-size: .72rem; color: rgba(255,255,255,0.65); text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px;">Nourriture Sauvée</div>
                <div style="font-size: 1.4rem; font-weight: 800; color: #fff; line-height: 1.2;">{{ Auth::user()->impact_metrics['kg_sauves'] }} <span style="font-size: .85rem; font-weight: 600; color: #4ade80;">kg</span></div>
            </div>
        </div>

        <!-- Card 2 -->
        <div style="background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.12); border-radius: 14px; padding: 16px; display: flex; align-items: center; gap: 14px;">
            <div style="width: 46px; height: 46px; border-radius: 12px; background: rgba(59,130,246,0.2); color: #60a5fa; display: flex; align-items: center; justify-content: center; font-size: 1.3rem;">
                🌍
            </div>
            <div>
                <div style="font-size: .72rem; color: rgba(255,255,255,0.65); text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px;">CO₂ Évité</div>
                <div style="font-size: 1.4rem; font-weight: 800; color: #fff; line-height: 1.2;">{{ Auth::user()->impact_metrics['co2_evite_kg'] }} <span style="font-size: .85rem; font-weight: 600; color: #60a5fa;">kg CO₂e</span></div>
            </div>
        </div>

        <!-- Card 3 -->
        <div style="background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.12); border-radius: 14px; padding: 16px; display: flex; align-items: center; gap: 14px;">
            <div style="width: 46px; height: 46px; border-radius: 12px; background: rgba(245,158,11,0.2); color: #fbbf24; display: flex; align-items: center; justify-content: center; font-size: 1.3rem;">
                💰
            </div>
            <div>
                <div style="font-size: .72rem; color: rgba(255,255,255,0.65); text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px;">Valeur Économisée</div>
                <div style="font-size: 1.4rem; font-weight: 800; color: #fff; line-height: 1.2;">{{ number_format(Auth::user()->impact_metrics['economies_fcfa'], 0, ',', ' ') }} <span style="font-size: .85rem; font-weight: 600; color: #fbbf24;">FCFA</span></div>
            </div>
        </div>
    </div>

    <!-- Badges Grid -->
    <div style="font-size: .85rem; font-weight: 700; color: #fff; margin-bottom: 12px; display: flex; align-items: center; gap: 6px;">
        🏆 Vos Trophées & Badges Débloqués
    </div>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 12px;">
        @foreach(Auth::user()->badges as $badge)
        <div style="background: {{ $badge['debloque'] ? 'rgba(255,255,255,0.1)' : 'rgba(255,255,255,0.03)' }}; border: 1px solid {{ $badge['debloque'] ? 'rgba(74,222,128,0.4)' : 'rgba(255,255,255,0.08)' }}; border-radius: 12px; padding: 12px; display: flex; gap: 12px; align-items: center; opacity: {{ $badge['debloque'] ? '1' : '0.65' }};">
            <div style="font-size: 1.8rem; width: 44px; height: 44px; display: flex; align-items: center; justify-content: center; background: rgba(0,0,0,0.2); border-radius: 50%;">
                {{ $badge['icone'] }}
            </div>
            <div style="flex: 1; min-width: 0;">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span style="font-size: .82rem; font-weight: 800; color: {{ $badge['debloque'] ? '#4ade80' : '#d1d5db' }};">{{ $badge['nom'] }}</span>
                    @if($badge['debloque'])
                    <span style="background: #22c55e; color: #fff; font-size: .6rem; font-weight: 800; padding: 2px 6px; border-radius: 50px;">✔ ACCOMPLI</span>
                    @else
                    <span style="color: rgba(255,255,255,0.5); font-size: .65rem; font-weight: 700;">{{ $badge['progres'] }}%</span>
                    @endif
                </div>
                <p style="font-size: .72rem; color: rgba(255,255,255,0.6); margin: 3px 0 6px 0; line-height: 1.2;">{{ $badge['description'] }}</p>
                @if(!$badge['debloque'])
                <div style="width: 100%; height: 4px; background: rgba(255,255,255,0.1); border-radius: 2px; overflow: hidden;">
                    <div style="width: {{ $badge['progres'] }}%; height: 100%; background: #3b82f6;"></div>
                </div>
                @endif
            </div>
        </div>
        @endforeach
    </div>
</div>

{{-- ── CASHFLOW + SIDE METRICS ── --}}
<div class="cashflow-grid">
    <div class="cashflow-card">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:18px;">
            <span style="font-size:.95rem;font-weight:700;color:#1a1a2e;"><i class="fas fa-chart-bar me-2" style="color:#16a34a;"></i>Vues des annonces</span>
            <div class="cashflow-tabs">
                <button class="cf-tab active">7 jours</button>
                <button class="cf-tab">Mensuel</button>
            </div>
        </div>
        <canvas id="cashflowChart" height="110"></canvas>
    </div>

    <div class="side-metric-card">
        <div class="side-metric">
            <div class="sm-icon" style="background:#f0fdf4;"><i class="fas fa-handshake" style="color:#16a34a;font-size:.9rem;"></i></div>
            <div class="sm-label">Réservations reçues</div>
            <div class="sm-value">{{ $stats['reservations_att'] + $stats['echanges_completes'] }}</div>
            <div class="sm-trend trend-up"><i class="fas fa-arrow-up" style="font-size:.6rem;"></i> {{ $stats['echanges_completes'] }} complétées</div>
        </div>
        <div class="side-metric">
            <div class="sm-icon" style="background:#fef9c3;"><i class="fas fa-clock" style="color:#b45309;font-size:.9rem;"></i></div>
            <div class="sm-label">En attente traitement</div>
            <div class="sm-value">{{ $stats['reservations_att'] }}</div>
            <div class="sm-trend trend-neutral">
                {{ $stats['reservations_att'] > 0 ? 'À traiter' : 'Aucune en attente' }}
            </div>
        </div>
    </div>
</div>

{{-- ── IMPACT ÉCOLOGIQUE ── --}}
<div style="background:linear-gradient(135deg,#052e16 0%,#14532d 100%);border-radius:20px;padding:28px 32px;margin-bottom:24px;color:#fff;">
    <div style="display:flex;align-items:center;gap:10px;margin-bottom:20px;">
        <span style="font-size:1.4rem;">🌍</span>
        <div>
            <div style="font-weight:800;font-size:1rem;">Votre impact écologique & social</div>
            <div style="font-size:.78rem;opacity:.7;">Calculé sur la totalité de vos échanges complétés</div>
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
            <div style="font-size:1.8rem;font-weight:900;color:#6ee7b7;">{{ $impact['revenus_nets'] }} F</div>
            <div style="font-size:.75rem;opacity:.8;margin-top:4px;">💰 Revenus nets</div>
            <div style="font-size:.6rem;opacity:.6;margin-top:2px;">(-5% frais de plateforme)</div>
        </div>
    </div>
</div>

{{-- ── 3 METRIC CARDS ── --}}
<div class="metric-row">
    <div class="metric-card">
        <div class="mc-header">
            <span class="mc-title">Annonces actives</span>
            <span class="mc-period">En ligne</span>
        </div>
        <div class="mc-value">{{ $stats['annonces_actives'] }}</div>
        <div class="mc-trend trend-up"><i class="fas fa-circle" style="font-size:.45rem;"></i> Disponibles maintenant</div>
        <div class="mc-compare">sur {{ $dernieresAnnonces->count() }} annonces récentes</div>
    </div>
    <div class="metric-card">
        <div class="mc-header">
            <span class="mc-title">Réservations en attente</span>
            <span class="mc-period">À traiter</span>
        </div>
        <div class="mc-value" style="{{ $stats['reservations_att'] > 0 ? 'color:#b45309;' : '' }}">{{ $stats['reservations_att'] }}</div>
        <div class="mc-trend {{ $stats['reservations_att'] > 0 ? 'trend-neutral' : 'trend-up' }}">
            {{ $stats['reservations_att'] > 0 ? 'Réponse requise' : 'Tout est traité ✓' }}
        </div>
        <div class="mc-compare">
            <a href="{{ route('reservations.mes-reservations') }}" style="color:#16a34a;font-size:.72rem;text-decoration:none;font-weight:600;">Voir toutes →</a>
        </div>
    </div>
    <div class="metric-card">
        <div class="mc-header">
            <span class="mc-title">Échanges complétés</span>
            <span class="mc-period">Total</span>
        </div>
        <div class="mc-value">{{ $stats['echanges_completes'] }}</div>
        <div class="mc-trend trend-up"><i class="fas fa-arrow-up" style="font-size:.6rem;"></i>
            Note : {{ $stats['note_moyenne'] > 0 ? $stats['note_moyenne'].' ★' : 'Aucun avis' }}
        </div>
        <div class="mc-compare">satisfaction fournisseur</div>
    </div>
</div>

{{-- ── OFFRES EN ATTENTE (Négociation de prix) ── --}}

@if($offresEnAttente->count() > 0)
<div style="background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%); border: 2px solid #f59e0b; border-radius: 16px; padding: 20px 22px; margin-bottom: 24px;">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px;flex-wrap:wrap;gap:8px;">
        <div style="display:flex;align-items:center;gap:10px;">
            <div style="width:38px;height:38px;border-radius:10px;background:#fbbf24;display:flex;align-items:center;justify-content:center;font-size:1.1rem;">
                🤝
            </div>
            <div>
                <div style="font-weight:800;font-size:.95rem;color:#1a1a2e;">Offres de prix en attente</div>
                <div style="font-size:.76rem;color:#92400e;">{{ $offresEnAttente->count() }} acheteur(s) attendent votre réponse</div>
            </div>
        </div>
        <a href="{{ route('messages.index') }}" style="font-size:.76rem;font-weight:600;color:#92400e;text-decoration:none;background:rgba(251,191,36,.3);padding:5px 14px;border-radius:50px;">
            Voir tous les messages →
        </a>
    </div>

    <div style="display:flex;flex-direction:column;gap:8px;">
        @foreach($offresEnAttente as $offreDash)
        <div style="background:#fff;border-radius:10px;padding:12px 14px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;box-shadow:0 1px 4px rgba(0,0,0,.05);">
            <div style="display:flex;align-items:center;gap:10px;">
                <div style="width:34px;height:34px;border-radius:50%;background:#eff6ff;display:flex;align-items:center;justify-content:center;font-weight:700;color:#3b82f6;font-size:.85rem;">
                    {{ strtoupper(substr($offreDash->acheteur->prenom ?? '?', 0, 1)) }}
                </div>
                <div>
                    <div style="font-weight:600;font-size:.83rem;color:#1a1a2e;">
                        {{ $offreDash->acheteur->prenom ?? '—' }} propose
                        <strong style="color:#0c4a6e;">{{ number_format($offreDash->prix_propose, 0, ',', ' ') }} FCFA</strong>
                    </div>
                    <div style="font-size:.73rem;color:#64748b;">
                        {{ Str::limit($offreDash->annonce->titre ?? '—', 40) }} ·
                        {{ $offreDash->created_at->diffForHumans() }}
                    </div>
                </div>
            </div>
            <div style="display:flex;gap:6px;">
                <form action="{{ route('offres.accepter', $offreDash) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-success rounded-pill px-3 fw-semibold" style="font-size:.76rem;" title="Accepter l'offre">
                        <i class="fas fa-check me-1"></i>Accepter
                    </button>
                </form>
                <form action="{{ route('offres.refuser', $offreDash) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-danger rounded-pill px-3 fw-semibold" style="font-size:.76rem;" title="Refuser l'offre">
                        <i class="fas fa-times me-1"></i>Refuser
                    </button>
                </form>
                @if($offreDash->conversation)
                <a href="{{ route('messages.show', $offreDash->conversation) }}" class="btn btn-sm btn-outline-warning rounded-pill px-2" style="font-size:.76rem;" title="Discuter">
                    <i class="fas fa-comment"></i>
                </a>
                @endif
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif

{{-- ── RECENT ACTIVITY ── --}}
<div class="card">
    <div class="section-header">
        <span class="section-title"><i class="fas fa-bolt me-2" style="color:#f59e0b;"></i>Activité récente</span>
        <div class="header-actions">
            <a href="{{ route('reservations.mes-reservations') }}" class="btn-filter"><i class="fas fa-filter"></i> Filtrer</a>
            <a href="{{ route('reservations.mes-reservations') }}" class="btn-filter"><i class="fas fa-external-link-alt"></i> Voir tout</a>
        </div>
    </div>
    @if($dernieresReservations->isEmpty())
    <div style="text-align:center;padding:40px;color:#bbb;">
        <i class="fas fa-inbox" style="font-size:2.5rem;display:block;margin-bottom:12px;opacity:.2;"></i>
        <p style="font-size:.85rem;margin-bottom:14px;">Aucune réservation reçue pour l'instant.</p>
        <a href="{{ route('annonces.create') }}" style="background:#16a34a;color:#fff;padding:10px 24px;border-radius:50px;text-decoration:none;font-size:.82rem;font-weight:600;">
            <i class="fas fa-plus me-2"></i>Publier une annonce
        </a>
    </div>
    @else
    <table class="dash-table">
        <thead>
            <tr>
                <th>TYPE</th>
                <th>ANNONCE</th>
                <th>ACHETEUR</th>
                <th>QUANTITÉ</th>
                <th>STATUT</th>
                <th>MÉTHODE</th>
            </tr>
        </thead>
        <tbody>
        @foreach($dernieresReservations as $r)
        <tr>
            <td>
                <div style="display:flex;align-items:center;gap:8px;">
                    <div style="width:30px;height:30px;border-radius:8px;background:#f0fdf4;display:flex;align-items:center;justify-content:center;">
                        <i class="fas fa-handshake" style="font-size:.7rem;color:#16a34a;"></i>
                    </div>
                    <span style="font-size:.78rem;font-weight:600;color:#1a1a2e;">Réservation</span>
                </div>
            </td>
            <td>
                <div style="font-weight:600;font-size:.82rem;">{{ Str::limit($r->annonce->titre??'—', 24) }}</div>
                <div style="font-size:.7rem;color:#bbb;">{{ $r->created_at->format('d/m/Y') }}</div>
            </td>
            <td>
                <div style="display:flex;align-items:center;gap:7px;">
                    <div style="width:26px;height:26px;border-radius:50%;background:#e5e7eb;display:flex;align-items:center;justify-content:center;font-size:.7rem;font-weight:700;">
                        {{ strtoupper(substr($r->acheteur->prenom??'?',0,1)) }}
                    </div>
                    <span style="font-size:.8rem;">{{ $r->acheteur->prenom??'—' }} {{ $r->acheteur->nom??'' }}</span>
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
                <div style="display:flex;gap:6px;">
                    <form action="{{ route('reservations.accepter',$r) }}" method="POST" style="display:inline;">
                        @csrf<button type="submit" style="background:#f0fdf4;color:#16a34a;border:none;border-radius:6px;padding:5px 10px;font-size:.72rem;font-weight:600;cursor:pointer;font-family:inherit;"><i class="fas fa-check me-1"></i>Accepter</button>
                    </form>
                    <form action="{{ route('reservations.refuser',$r) }}" method="POST" style="display:inline;">
                        @csrf<button type="submit" style="background:#fef2f2;color:#ef4444;border:none;border-radius:6px;padding:5px 10px;font-size:.72rem;font-weight:600;cursor:pointer;font-family:inherit;"><i class="fas fa-times me-1"></i>Refuser</button>
                    </form>
                </div>
                @elseif($r->statut === 'acceptée')
                <form action="{{ route('reservations.completer',$r) }}" method="POST" style="display:inline;">
                    @csrf<button type="submit" style="background:#dbeafe;color:#1d4ed8;border:none;border-radius:6px;padding:5px 10px;font-size:.72rem;font-weight:600;cursor:pointer;font-family:inherit;"><i class="fas fa-check-double me-1"></i>Compléter</button>
                </form>
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
    <div class="profile-avatar">{{ strtoupper(substr($user->prenom,0,1)) }}</div>
    <div class="profile-name">{{ $user->prenom }} {{ $user->nom }}</div>
    <div class="profile-role" style="margin-top:6px;">
        <span class="status-pill pill-green"><span class="pill-dot"></span>Fournisseur actif</span>
    </div>
    @if($user->nom_structure)
    <div style="font-size:.75rem;color:#888;margin-top:8px;background:#f9fafb;padding:6px 10px;border-radius:8px;">
        🏪 {{ $user->nom_structure }}
    </div>
    @endif
    @if($stats['note_moyenne'] > 0)
    <div style="margin-top:10px;">
        @for($i=1;$i<=5;$i++)<i class="fas fa-star" style="font-size:.85rem;color:{{ $i<=$stats['note_moyenne']?'#f59e0b':'#e5e7eb' }};"></i>@endfor
        <span style="font-size:.75rem;color:#888;margin-left:4px;">{{ $stats['note_moyenne'] }}/5</span>
    </div>
    @endif
    <div class="profile-actions">
        <a href="{{ route('messages.index') }}" class="profile-btn" title="Messages"><i class="fas fa-comment"></i></a>
        <a href="{{ route('profil.edit') }}" class="profile-btn" title="Profil"><i class="fas fa-cog"></i></a>
        <a href="{{ route('annonces.mes-annonces') }}" class="profile-btn" title="Annonces"><i class="fas fa-list"></i></a>
    </div>
</div>

{{-- MES ANNONCES ACTIVES --}}
<div class="card" style="padding:18px;">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px;">
        <span style="font-size:.85rem;font-weight:700;color:#1a1a2e;"><i class="fas fa-bullhorn me-2" style="color:#16a34a;font-size:.8rem;"></i>Mes annonces actives</span>
        <a href="{{ route('annonces.mes-annonces') }}" style="font-size:.72rem;color:#16a34a;font-weight:600;text-decoration:none;">Voir tout</a>
    </div>
    @forelse($dernieresAnnonces->where('statut','disponible')->take(3) as $a)
    <div class="annonce-list-card">
        <div style="display:flex;justify-content:space-between;align-items:flex-start;">
            <div style="flex:1;min-width:0;">
                <div style="font-size:.82rem;font-weight:700;color:#1a1a2e;margin-bottom:3px;">{{ Str::limit($a->titre,26) }}</div>
                <div style="font-size:.7rem;color:#999;">{{ $a->categorie->icone??'📦' }} {{ $a->categorie->nom??'—' }} · {{ $a->quantite }} {{ $a->unite }}</div>
            </div>
            <div style="font-size:.82rem;font-weight:700;color:#16a34a;white-space:nowrap;margin-left:8px;">
                {{ $a->type_offre==='don'?'Don':(($a->prix>0)?number_format($a->prix,0,',',' ').' F':'Libre') }}
            </div>
        </div>
        <div class="alc-bar"><div class="alc-fill" style="width:{{ min(100, ($a->vues / max(1, $stats['total_vues'])) * 100 * count($dernieresAnnonces)) }}%;"></div></div>
        <div style="display:flex;justify-content:space-between;align-items:center;margin-top:6px;">
            <span style="font-size:.68rem;color:#bbb;"><i class="fas fa-eye me-1"></i>{{ $a->vues }} vues</span>
            <a href="{{ route('annonces.edit',$a) }}" style="font-size:.68rem;color:#16a34a;text-decoration:none;font-weight:600;"><i class="fas fa-edit me-1"></i>Modifier</a>
        </div>
    </div>
    @empty
    <div style="text-align:center;padding:20px;color:#bbb;font-size:.78rem;">
        <i class="fas fa-box-open" style="display:block;font-size:1.5rem;margin-bottom:8px;opacity:.3;"></i>
        Aucune annonce active
    </div>
    @endforelse
    <a href="{{ route('annonces.create') }}" style="display:flex;align-items:center;justify-content:center;gap:8px;width:100%;padding:10px;background:#f0fdf4;color:#16a34a;border-radius:10px;text-decoration:none;font-size:.8rem;font-weight:600;margin-top:10px;border:1.5px dashed #bbf7d0;">
        <i class="fas fa-plus"></i> Nouvelle annonce
    </a>
</div>
@endsection

@push('scripts')
<script>
const ctx = document.getElementById('cashflowChart');
if(ctx){
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($chartLabels) !!},
            datasets: [{
                label: 'Vues',
                data: {!! json_encode($chartData) !!},
                backgroundColor: (context) => {
                    const val = context.raw;
                    return val >= 0 ? 'rgba(22,163,74,0.85)' : 'rgba(239,68,68,0.7)';
                },
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
                    callbacks: {
                        label: (ctx) => ' ' + ctx.raw + ' vues'
                    }
                }
            },
            scales: {
                y: { beginAtZero: true, grid: { color: '#f5f5f5' }, ticks: { color: '#bbb', font: { size: 10 } } },
                x: { grid: { display: false }, ticks: { color: '#bbb', font: { size: 10 } } }
            }
        }
    });
}
</script>
@endpush
