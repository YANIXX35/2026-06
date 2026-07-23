<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>@yield('title','Dashboard') — AntiGaspiCI</title>
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Rubik:wght@400;500;600;700;800;900&family=Nunito+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css"/>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<style>
:root{
    --green:#16a34a;--green-d:#15803d;--green-l:#22c55e;
    --green-50:#f0fdf4;--green-100:#dcfce7;
    --orange:#f97316;
    --sidebar:#0f172a;--sidebar-hover:rgba(255,255,255,.06);--sidebar-active:rgba(22,163,74,.15);
    --text:#0f172a;--muted:#64748b;--muted-2:#94a3b8;
    --border:#e2e8f0;--surface:#f8fafc;--white:#fff;
    --red:#ef4444;--yellow:#f59e0b;--blue:#2563eb;--purple:#7c3aed;
}
*{margin:0;padding:0;box-sizing:border-box;}
html{scroll-behavior:smooth;}
body{font-family:'Nunito Sans',sans-serif;background:#f1f5f9;color:var(--text);display:flex;min-height:100vh;overflow-x:hidden;}
h1,h2,h3,h4,h5{font-family:'Rubik',sans-serif;}
::-webkit-scrollbar{width:4px;}::-webkit-scrollbar-thumb{background:rgba(255,255,255,.15);border-radius:4px;}

/* ══════════════════════════════════════
   SIDEBAR
══════════════════════════════════════ */
.sidebar{
    width:230px;flex-shrink:0;
    background:var(--sidebar);
    display:flex;flex-direction:column;
    padding:0;
    position:fixed;top:0;left:0;height:100vh;
    z-index:200;overflow-y:auto;
    box-shadow:4px 0 24px rgba(0,0,0,.12);
}
.sidebar-logo{
    display:flex;align-items:center;gap:11px;
    padding:22px 20px 18px;
    border-bottom:1px solid rgba(255,255,255,.07);
    text-decoration:none;
}
.sl-ico{width:32px;height:32px;border-radius:9px;
    background:linear-gradient(135deg,var(--green),var(--green-l));
    display:flex;align-items:center;justify-content:center;font-size:.95rem;flex-shrink:0;}
.sl-txt{font-family:'Rubik',sans-serif;font-weight:800;font-size:1rem;color:#fff;line-height:1.2;}
.sl-txt small{display:block;font-size:.58rem;font-weight:500;color:rgba(255,255,255,.4);letter-spacing:.5px;margin-top:1px;}

.sidebar-nav{padding:16px 12px;flex:1;}
.nav-section{font-size:.6rem;font-weight:800;letter-spacing:2px;color:rgba(255,255,255,.25);
    text-transform:uppercase;padding:0 8px;margin:18px 0 6px;display:block;}
.nav-section:first-child{margin-top:0;}
.nav-item{
    display:flex;align-items:center;gap:11px;
    padding:10px 12px;border-radius:10px;
    text-decoration:none;color:rgba(255,255,255,.55);
    font-size:.83rem;font-weight:600;
    transition:all .2s;margin-bottom:2px;
    cursor:pointer;border:none;background:none;width:100%;text-align:left;
    font-family:'Nunito Sans',sans-serif;
}
.nav-item:hover{background:var(--sidebar-hover);color:rgba(255,255,255,.88);}
.nav-item.active{background:var(--sidebar-active);color:#4ade80;font-weight:700;}
.nav-item .nav-icon{width:18px;text-align:center;font-size:.85rem;flex-shrink:0;}
.nav-item .nav-badge{margin-left:auto;font-size:.6rem;font-weight:800;padding:2px 7px;
    border-radius:50px;color:#fff;line-height:1.4;}
.nav-item.nav-danger{color:rgba(239,68,68,.7);}
.nav-item.nav-danger:hover{background:rgba(239,68,68,.1);color:#ef4444;}

.sidebar-bottom{padding:12px;border-top:1px solid rgba(255,255,255,.07);}
.profile-mini{display:flex;align-items:center;gap:10px;padding:12px 10px;border-radius:10px;
    background:rgba(255,255,255,.05);margin-bottom:10px;}
.pm-av{width:34px;height:34px;border-radius:50%;
    background:linear-gradient(135deg,var(--green),var(--green-l));
    display:flex;align-items:center;justify-content:center;font-size:.95rem;flex-shrink:0;}
.pm-info{flex:1;min-width:0;}
.pm-name{font-size:.78rem;font-weight:700;color:#fff;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
.pm-role{font-size:.62rem;color:rgba(255,255,255,.4);margin-top:1px;}
.upgrade-card{background:linear-gradient(135deg,var(--green),var(--green-d));
    border-radius:12px;padding:14px;color:#fff;margin-bottom:10px;}
.upgrade-card strong{font-size:.8rem;display:block;margin-bottom:3px;}
.upgrade-card p{font-size:.7rem;opacity:.85;margin-bottom:9px;line-height:1.45;}
.btn-upgrade{display:block;background:rgba(255,255,255,.2);color:#fff;text-align:center;
    padding:7px;border-radius:8px;font-size:.75rem;font-weight:700;text-decoration:none;
    border:1px solid rgba(255,255,255,.25);transition:background .2s;}
.btn-upgrade:hover{background:rgba(255,255,255,.3);color:#fff;}

/* ══════════════════════════════════════
   TOPBAR
══════════════════════════════════════ */
.topbar{
    position:fixed;top:0;left:230px;right:0;height:60px;z-index:100;
    background:rgba(255,255,255,.95);backdrop-filter:blur(12px);
    border-bottom:1px solid var(--border);
    display:flex;align-items:center;justify-content:space-between;
    padding:0 28px;
}
.topbar-left{display:flex;align-items:center;gap:12px;}
.topbar-title{font-family:'Rubik',sans-serif;font-size:.95rem;font-weight:800;color:var(--text);}
.topbar-sep{color:var(--border);}
.topbar-sub{font-size:.78rem;color:var(--muted);}
.topbar-right{display:flex;align-items:center;gap:10px;}
.tb-btn{width:36px;height:36px;border-radius:50%;background:var(--surface);border:1px solid var(--border);
    display:flex;align-items:center;justify-content:center;
    color:var(--muted);font-size:.82rem;cursor:pointer;text-decoration:none;transition:all .2s;position:relative;}
.tb-btn:hover{background:var(--green-50);color:var(--green);border-color:var(--green-100);}
.tb-badge{position:absolute;top:-3px;right:-3px;background:var(--red);color:#fff;
    font-size:.5rem;font-weight:800;border-radius:50%;width:14px;height:14px;
    display:flex;align-items:center;justify-content:center;border:2px solid #fff;}
.tb-user{display:flex;align-items:center;gap:8px;padding:5px 10px 5px 5px;
    border-radius:50px;background:var(--surface);border:1px solid var(--border);
    cursor:pointer;transition:all .2s;}
.tb-user:hover{border-color:var(--green-100);background:var(--green-50);}
.tb-avatar{width:28px;height:28px;border-radius:50%;
    background:linear-gradient(135deg,var(--green),var(--green-l));
    display:flex;align-items:center;justify-content:center;font-size:.8rem;font-weight:700;color:#fff;}
.tb-uname{font-size:.78rem;font-weight:700;color:var(--text);}

/* ══════════════════════════════════════
   LAYOUT
══════════════════════════════════════ */
.main-wrapper{margin-left:230px;flex:1;display:flex;flex-direction:column;min-height:100vh;padding-top:60px;}
.main-content{flex:1;padding:28px;min-width:0;}
.page-header{display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:24px;flex-wrap:wrap;gap:12px;}
.page-header h1{font-size:1.45rem;font-weight:900;color:var(--text);letter-spacing:-.5px;}
.page-header .date-badge{display:flex;align-items:center;gap:8px;background:#fff;
    border:1px solid var(--border);padding:8px 16px;border-radius:50px;
    font-size:.78rem;color:var(--muted);box-shadow:0 1px 4px rgba(0,0,0,.04);}

/* ══════════════════════════════════════
   STAT CARDS
══════════════════════════════════════ */
.stats-row{display:grid;grid-template-columns:repeat(auto-fit,minmax(155px,1fr));gap:16px;margin-bottom:24px;}
.stat-card{
    background:#fff;border-radius:18px;padding:20px;
    display:flex;align-items:center;gap:14px;
    border:1px solid var(--border);
    box-shadow:0 1px 6px rgba(0,0,0,.04);
    transition:all .25s;position:relative;overflow:hidden;
}
.stat-card::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;border-radius:18px 18px 0 0;
    background:var(--sc-clr,linear-gradient(90deg,var(--green),var(--green-l)));}
.stat-card:hover{transform:translateY(-3px);box-shadow:0 8px 24px rgba(0,0,0,.08);}
.stat-icon{width:46px;height:46px;border-radius:13px;
    display:flex;align-items:center;justify-content:center;font-size:1.15rem;flex-shrink:0;}
.stat-value{font-family:'Rubik',sans-serif;font-size:1.65rem;font-weight:900;line-height:1;color:var(--text);}
.stat-label{font-size:.72rem;color:var(--muted);margin-top:3px;font-weight:600;}
.stat-trend{font-size:.7rem;font-weight:700;margin-top:5px;display:inline-flex;align-items:center;gap:4px;
    padding:2px 8px;border-radius:50px;}
.stat-trend.up{background:#f0fdf4;color:var(--green);}
.stat-trend.down{background:#fef2f2;color:var(--red);}
.stat-trend.neutral{background:#fefce8;color:var(--yellow);}

/* ══════════════════════════════════════
   CARDS
══════════════════════════════════════ */
.card{background:#fff;border-radius:18px;padding:22px;
    border:1px solid var(--border);box-shadow:0 1px 6px rgba(0,0,0,.04);margin-bottom:20px;}
.card-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:18px;}
.card-title{font-family:'Rubik',sans-serif;font-size:.92rem;font-weight:800;color:var(--text);
    display:flex;align-items:center;gap:8px;}
.card-action{font-size:.76rem;color:var(--green);text-decoration:none;font-weight:700;
    background:var(--green-50);padding:5px 13px;border-radius:50px;transition:all .2s;}
.card-action:hover{background:var(--green-100);color:var(--green-d);}

/* ══════════════════════════════════════
   TABLE
══════════════════════════════════════ */
.dash-table{width:100%;border-collapse:collapse;}
.dash-table th{font-size:.68rem;font-weight:800;color:var(--muted);
    text-transform:uppercase;letter-spacing:1px;padding:0 14px 12px;text-align:left;}
.dash-table td{padding:11px 14px;border-top:1px solid #f1f5f9;font-size:.82rem;vertical-align:middle;}
.dash-table tbody tr{transition:background .15s;}
.dash-table tbody tr:hover td{background:#fafafa;}
.status-pill{display:inline-flex;align-items:center;gap:5px;padding:4px 11px;
    border-radius:50px;font-size:.7rem;font-weight:700;}
.pill-green {background:#dcfce7;color:#15803d;}
.pill-yellow{background:#fef9c3;color:#92400e;}
.pill-red   {background:#fee2e2;color:#b91c1c;}
.pill-blue  {background:#dbeafe;color:#1d4ed8;}
.pill-purple{background:#f3e8ff;color:#6d28d9;}
.pill-gray  {background:#f1f5f9;color:#64748b;}
.pill-dot{width:5px;height:5px;border-radius:50%;background:currentColor;flex-shrink:0;}

/* ══════════════════════════════════════
   CHART
══════════════════════════════════════ */
.chart-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;}
.chart-period{font-size:.72rem;color:var(--muted);background:var(--surface);
    padding:4px 12px;border-radius:50px;border:1px solid var(--border);}

/* ══════════════════════════════════════
   RIGHT PANEL
══════════════════════════════════════ */
.right-panel{width:280px;flex-shrink:0;padding:28px 20px 28px 4px;}
.profile-card{background:linear-gradient(145deg,#0f172a 0%,#1e293b 100%);
    border-radius:18px;padding:24px;text-align:center;margin-bottom:16px;
    border:1px solid rgba(255,255,255,.07);
    box-shadow:0 4px 20px rgba(0,0,0,.12);}
.profile-avatar{width:68px;height:68px;border-radius:50%;
    display:flex;align-items:center;justify-content:center;
    font-size:1.6rem;font-weight:800;margin:0 auto 12px;}
.profile-name{font-family:'Rubik',sans-serif;font-size:.95rem;font-weight:800;color:#fff;}
.profile-role-badge{display:inline-block;margin-top:6px;
    background:rgba(34,197,94,.15);border:1px solid rgba(34,197,94,.3);
    color:#4ade80;font-size:.68rem;font-weight:700;padding:3px 12px;border-radius:50px;}
.profile-actions{display:flex;justify-content:center;gap:8px;margin-top:16px;}
.profile-btn{width:34px;height:34px;border-radius:50%;border:1px solid rgba(255,255,255,.12);
    display:flex;align-items:center;justify-content:center;
    color:rgba(255,255,255,.5);font-size:.8rem;cursor:pointer;
    transition:all .2s;text-decoration:none;background:rgba(255,255,255,.05);}
.profile-btn:hover{background:rgba(22,163,74,.2);border-color:rgba(22,163,74,.4);color:#4ade80;}

.activity-card{background:#fff;border-radius:18px;padding:20px;
    border:1px solid var(--border);box-shadow:0 1px 6px rgba(0,0,0,.04);}
.activity-title{font-family:'Rubik',sans-serif;font-size:.85rem;font-weight:800;
    color:var(--text);margin-bottom:14px;}
.activity-item{display:flex;gap:10px;padding:10px 0;border-bottom:1px solid #f1f5f9;}
.activity-item:last-child{border-bottom:none;padding-bottom:0;}
.activity-avatar{width:32px;height:32px;border-radius:50%;background:var(--surface);
    display:flex;align-items:center;justify-content:center;
    font-size:.72rem;font-weight:800;flex-shrink:0;border:1px solid var(--border);}
.activity-text{font-size:.75rem;color:var(--muted);line-height:1.5;flex:1;}
.activity-text strong{color:var(--text);font-weight:700;}
.activity-time{font-size:.65rem;color:var(--muted-2);margin-top:2px;}

/* ══════════════════════════════════════
   FLASH
══════════════════════════════════════ */
.flash-ok{background:#f0fdf4;border:1px solid #bbf7d0;color:#15803d;
    border-radius:12px;padding:12px 18px;margin-bottom:20px;
    font-size:.84rem;font-weight:600;display:flex;align-items:center;gap:8px;}
.flash-err{background:#fef2f2;border:1px solid #fecaca;color:#b91c1c;
    border-radius:12px;padding:12px 18px;margin-bottom:20px;
    font-size:.84rem;font-weight:600;display:flex;align-items:center;gap:8px;}

/* ══════════════════════════════════════
   EMPTY
══════════════════════════════════════ */
.empty-row td{text-align:center;padding:40px;color:var(--muted-2);}
.empty-row i{font-size:1.5rem;display:block;margin-bottom:8px;opacity:.5;}

/* ══════════════════════════════════════
   RESPONSIVE
══════════════════════════════════════ */
@media(max-width:1200px){.right-panel{display:none;}}
.sidebar-backdrop{display:none;position:fixed;inset:0;background:rgba(0,0,0,.4);z-index:190;}
.sidebar-backdrop.open{display:block;}
#menuBtn{display:none;}
@media(max-width:900px){
    .sidebar{transform:translateX(-100%);transition:.3s;}
    .sidebar.open{transform:translateX(0);}
    .main-wrapper{margin-left:0;}
    .topbar{left:0;}
    .stats-row{grid-template-columns:1fr 1fr;}
    #menuBtn{display:block;}
    .dash-table{display:block;overflow-x:auto;white-space:nowrap;}
}
@media(max-width:480px){.stats-row{grid-template-columns:1fr;}}
</style>
@stack('styles')
</head>
<body>

<!-- ── SIDEBAR ── -->
<div class="sidebar-backdrop" id="sidebarBackdrop" onclick="closeSidebar()"></div>
<aside class="sidebar" id="sidebar">
    <a href="{{ route('home') }}" class="sidebar-logo">
        <div class="sl-ico">🌿</div>
        <div class="sl-txt">AntiGaspiCI <small>PLATEFORME ADMIN</small></div>
    </a>
    <nav class="sidebar-nav">
        @yield('sidebar-nav')
    </nav>
    <div class="sidebar-bottom">
        @auth
        <div class="profile-mini">
            <div class="pm-av">{{ strtoupper(substr(Auth::user()->prenom ?? 'A', 0, 1)) }}</div>
            <div class="pm-info">
                <div class="pm-name">{{ Auth::user()->prenom }} {{ Auth::user()->nom }}</div>
                <div class="pm-role">{{ ucfirst(Auth::user()->role) }}</div>
            </div>
        </div>
        @endauth
        <div class="upgrade-card">
            <strong>Complétez votre profil</strong>
            <p>Ajoutez photo et adresse pour plus de visibilité</p>
            <a href="{{ route('profil.edit') }}" class="btn-upgrade">Modifier le profil</a>
        </div>
        <a href="{{ route('contact') }}" class="nav-item">
            <span class="nav-icon"><i class="fas fa-question-circle"></i></span> Aide &amp; Support
        </a>
        <form action="{{ route('deconnecter') }}" method="POST">
            @csrf
            <button type="submit" class="nav-item nav-danger">
                <span class="nav-icon"><i class="fas fa-sign-out-alt"></i></span> Déconnexion
            </button>
        </form>
    </div>
</aside>

<!-- ── TOPBAR ── -->
<header class="topbar">
    <div class="topbar-left">
        <button onclick="toggleSidebar()"
            style="background:none;border:none;cursor:pointer;font-size:1.1rem;color:var(--muted);padding:4px;" id="menuBtn">
            <i class="fas fa-bars"></i>
        </button>
        <span class="topbar-title">@yield('page-title','Dashboard')</span>
    </div>
    <div class="topbar-right">
        @auth
        @php $unread = Auth::user()->unreadNotifications()->count(); @endphp
        <a href="{{ route('notifications.index') }}" class="tb-btn" title="Notifications">
            <i class="fas fa-bell"></i>
            @if($unread > 0)<span class="tb-badge">{{ min($unread,9) }}</span>@endif
        </a>
        <a href="{{ route('home') }}" class="tb-btn" title="Voir le site">
            <i class="fas fa-external-link-alt"></i>
        </a>
        <div class="tb-user">
            <div class="tb-avatar">{{ strtoupper(substr(Auth::user()->prenom ?? 'A', 0, 1)) }}</div>
            <span class="tb-uname">{{ Auth::user()->prenom }}</span>
        </div>
        @endauth
    </div>
</header>

<!-- ── MAIN ── -->
<div class="main-wrapper">
    <main class="main-content">
        @if(session('success'))
        <div class="flash-ok"><i class="fas fa-check-circle"></i>{{ session('success') }}</div>
        @endif
        @if(session('error'))
        <div class="flash-err"><i class="fas fa-times-circle"></i>{{ session('error') }}</div>
        @endif
        @yield('content')
    </main>
    <aside class="right-panel">
        @yield('right-panel')
    </aside>
</div>

<script>
function toggleSidebar(){
    document.getElementById('sidebar').classList.toggle('open');
    document.getElementById('sidebarBackdrop').classList.toggle('open');
}
function closeSidebar(){
    document.getElementById('sidebar').classList.remove('open');
    document.getElementById('sidebarBackdrop').classList.remove('open');
}
</script>
@stack('scripts')
</body>
</html>
