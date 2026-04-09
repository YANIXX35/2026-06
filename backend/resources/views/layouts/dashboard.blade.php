<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>@yield('title', 'Dashboard') — AntiGaspiCI</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css"/>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<style>
*{margin:0;padding:0;box-sizing:border-box;}
body{font-family:'Inter',sans-serif;background:#f0f2f5;color:#1a1a2e;display:flex;min-height:100vh;}

/* ── SIDEBAR ── */
.sidebar{
    width:220px;flex-shrink:0;background:#fff;
    display:flex;flex-direction:column;
    padding:24px 16px;
    position:fixed;top:0;left:0;height:100vh;
    border-right:1px solid #f0f0f0;
    z-index:100;overflow-y:auto;
}
.sidebar-logo{
    display:flex;align-items:center;gap:10px;
    font-weight:800;font-size:1.1rem;color:#16a34a;
    text-decoration:none;margin-bottom:32px;padding:0 8px;
}
.sidebar-logo span{color:#1a1a2e;font-size:.7rem;font-weight:500;opacity:.6;display:block;line-height:1;}
.nav-section{font-size:.65rem;font-weight:700;letter-spacing:1.5px;color:#b0b0b0;text-transform:uppercase;padding:0 8px;margin:16px 0 8px;}
.nav-item{
    display:flex;align-items:center;gap:12px;
    padding:10px 12px;border-radius:10px;
    text-decoration:none;color:#666;
    font-size:.85rem;font-weight:500;
    transition:all .2s;margin-bottom:2px;
    cursor:pointer;
}
.nav-item:hover{background:#f4f6f8;color:#1a1a2e;}
.nav-item.active{background:#f0fdf4;color:#16a34a;font-weight:600;}
.nav-item.active .nav-icon{color:#16a34a;}
.nav-item .nav-icon{width:18px;text-align:center;font-size:.9rem;color:#999;}
.nav-item .badge{margin-left:auto;background:#ef4444;color:#fff;font-size:.65rem;font-weight:700;padding:2px 7px;border-radius:50px;}
.nav-item.danger{color:#ef4444;}
.nav-item.danger:hover{background:#fef2f2;}
.nav-item.danger .nav-icon{color:#ef4444;}

.sidebar-bottom{margin-top:auto;padding-top:16px;border-top:1px solid #f0f0f0;}
.upgrade-card{background:linear-gradient(135deg,#16a34a,#22c55e);border-radius:14px;padding:16px;color:#fff;margin-bottom:12px;}
.upgrade-card p{font-size:.75rem;opacity:.9;margin-bottom:10px;line-height:1.4;}
.upgrade-card strong{font-size:.85rem;display:block;margin-bottom:4px;}
.btn-upgrade{display:block;background:#fff;color:#16a34a;text-align:center;padding:8px;border-radius:8px;font-size:.78rem;font-weight:700;text-decoration:none;transition:opacity .2s;}
.btn-upgrade:hover{opacity:.9;color:#16a34a;}

/* ── MAIN WRAPPER ── */
.main-wrapper{margin-left:220px;flex:1;display:flex;min-height:100vh;}
.main-content{flex:1;padding:28px 24px;min-width:0;}
.right-panel{width:290px;flex-shrink:0;padding:28px 20px 28px 0;}

/* ── TOP HEADER ── */
.page-header{display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:24px;flex-wrap:wrap;gap:12px;}
.page-header h1{font-size:1.5rem;font-weight:800;color:#1a1a2e;letter-spacing:-.3px;}
.page-header .date-badge{display:flex;align-items:center;gap:8px;background:#fff;border:1px solid #eee;padding:8px 14px;border-radius:10px;font-size:.8rem;color:#888;}

/* ── STAT CARDS ── */
.stats-row{display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:16px;margin-bottom:24px;}
.stat-card{background:#fff;border-radius:16px;padding:18px 20px;display:flex;align-items:center;gap:14px;box-shadow:0 1px 4px rgba(0,0,0,.06);}
.stat-icon{width:44px;height:44px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.2rem;flex-shrink:0;}
.stat-value{font-size:1.5rem;font-weight:800;line-height:1;color:#1a1a2e;}
.stat-label{font-size:.72rem;color:#999;margin-top:3px;font-weight:500;}
.stat-trend{font-size:.72rem;font-weight:600;margin-top:4px;}
.stat-trend.up{color:#16a34a;} .stat-trend.down{color:#ef4444;} .stat-trend.neutral{color:#f59e0b;}

/* ── CARD ── */
.card{background:#fff;border-radius:16px;padding:22px;box-shadow:0 1px 4px rgba(0,0,0,.06);margin-bottom:20px;}
.card-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:18px;}
.card-title{font-size:.95rem;font-weight:700;color:#1a1a2e;}
.card-action{font-size:.78rem;color:#16a34a;text-decoration:none;font-weight:600;background:#f0fdf4;padding:5px 12px;border-radius:8px;}
.card-action:hover{background:#dcfce7;}

/* ── TABLE ── */
.dash-table{width:100%;border-collapse:collapse;}
.dash-table th{font-size:.72rem;font-weight:700;color:#999;text-transform:uppercase;letter-spacing:.5px;padding:0 12px 12px;text-align:left;}
.dash-table td{padding:10px 12px;border-top:1px solid #f5f5f5;font-size:.82rem;vertical-align:middle;}
.dash-table tr:hover td{background:#fafafa;}
.status-pill{display:inline-flex;align-items:center;gap:5px;padding:4px 10px;border-radius:50px;font-size:.72rem;font-weight:600;}
.pill-green {background:#dcfce7;color:#16a34a;}
.pill-yellow{background:#fef9c3;color:#b45309;}
.pill-red   {background:#fee2e2;color:#dc2626;}
.pill-blue  {background:#dbeafe;color:#1d4ed8;}
.pill-gray  {background:#f3f4f6;color:#6b7280;}
.pill-dot{width:6px;height:6px;border-radius:50%;background:currentColor;}

/* ── CHART WRAPPER ── */
.chart-row{display:grid;grid-template-columns:1fr;gap:16px;}
.chart-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;}
.chart-period{font-size:.75rem;color:#888;background:#f5f5f5;padding:5px 12px;border-radius:8px;}

/* ── RIGHT PANEL ── */
.profile-card{background:#fff;border-radius:16px;padding:24px;text-align:center;box-shadow:0 1px 4px rgba(0,0,0,.06);margin-bottom:20px;}
.profile-avatar{width:72px;height:72px;border-radius:50%;background:linear-gradient(135deg,#16a34a,#22c55e);margin:0 auto 12px;display:flex;align-items:center;justify-content:center;font-size:1.8rem;font-weight:800;color:#fff;}
.profile-name{font-size:1rem;font-weight:700;color:#1a1a2e;}
.profile-role{font-size:.78rem;color:#999;margin-top:2px;}
.profile-actions{display:flex;justify-content:center;gap:10px;margin-top:16px;}
.profile-btn{width:36px;height:36px;border-radius:50%;border:1px solid #eee;display:flex;align-items:center;justify-content:center;color:#666;font-size:.85rem;cursor:pointer;transition:all .2s;text-decoration:none;}
.profile-btn:hover{background:#f0fdf4;border-color:#16a34a;color:#16a34a;}

.activity-card{background:#fff;border-radius:16px;padding:20px;box-shadow:0 1px 4px rgba(0,0,0,.06);}
.activity-title{font-size:.85rem;font-weight:700;color:#1a1a2e;margin-bottom:16px;}
.activity-item{display:flex;gap:10px;padding:10px 0;border-bottom:1px solid #f5f5f5;}
.activity-item:last-child{border-bottom:none;}
.activity-avatar{width:34px;height:34px;border-radius:50%;background:#e5e7eb;display:flex;align-items:center;justify-content:center;font-size:.85rem;flex-shrink:0;}
.activity-text{font-size:.78rem;color:#555;line-height:1.5;flex:1;}
.activity-text strong{color:#1a1a2e;font-weight:600;}
.activity-time{font-size:.68rem;color:#bbb;margin-top:2px;}

.message-input-bar{display:flex;gap:8px;margin-top:14px;padding-top:14px;border-top:1px solid #f0f0f0;}
.message-input-bar input{flex:1;background:#f5f5f5;border:none;padding:8px 12px;border-radius:8px;font-size:.8rem;outline:none;font-family:'Inter',sans-serif;}
.message-input-bar button{width:32px;height:32px;background:#16a34a;border:none;border-radius:8px;color:#fff;cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:.8rem;}

@media(max-width:1024px){.right-panel{display:none;} .main-wrapper{flex-direction:column;}}
@media(max-width:768px){.sidebar{transform:translateX(-100%);transition:.3s;} .main-wrapper{margin-left:0;} .stats-row{grid-template-columns:1fr 1fr;}}
</style>
@stack('styles')
</head>
<body>

<!-- ── SIDEBAR ── -->
<aside class="sidebar">
    <a href="{{ route('home') }}" class="sidebar-logo">
        <span style="font-size:1.4rem;">🌿</span>
        <div><span style="font-size:1rem;font-weight:800;color:#16a34a;">AntiGaspi</span><span style="color:#1a1a2e;">CI</span>
        <span>Plateforme alimentaire</span></div>
    </a>

    @yield('sidebar-nav')

    <div class="sidebar-bottom">
        <div class="upgrade-card">
            <strong>Complétez votre profil</strong>
            <p>Ajoutez photo et adresse pour plus de visibilité</p>
            <a href="{{ route('profil.edit') }}" class="btn-upgrade">Modifier</a>
        </div>
        <a href="{{ route('contact') }}" class="nav-item">
            <span class="nav-icon"><i class="fas fa-question-circle"></i></span> Aide & Support
        </a>
        <form action="{{ route('deconnecter') }}" method="POST">
            @csrf
            <button type="submit" class="nav-item danger" style="width:100%;border:none;background:none;text-align:left;">
                <span class="nav-icon"><i class="fas fa-sign-out-alt"></i></span> Déconnexion
            </button>
        </form>
    </div>
</aside>

<!-- ── MAIN WRAPPER ── -->
<div class="main-wrapper">
    <main class="main-content">
        @if(session('success'))
            <div style="background:#f0fdf4;border:1px solid #bbf7d0;color:#16a34a;padding:12px 18px;border-radius:10px;margin-bottom:20px;font-size:.85rem;font-weight:600;">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            </div>
        @endif
        @yield('content')
    </main>
    <aside class="right-panel">
        @yield('right-panel')
    </aside>
</div>

@stack('scripts')
</body>
</html>
