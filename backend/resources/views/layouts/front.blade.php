<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>@yield('title','AntiGaspiCI') — Plateforme Anti-Gaspillage Alimentaire</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="@yield('description','Plateforme collaborative de réduction du gaspillage alimentaire en Côte d\'Ivoire')">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Rubik:wght@400;500;600;700;800;900&family=Nunito+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css"/>
    @stack('styles')
    <style>
        :root{
            --green:#16a34a;--green-dark:#15803d;--green-light:#22c55e;
            --green-50:#f0fdf4;--green-100:#dcfce7;
            --orange:#f97316;--orange-dark:#ea580c;
            --dark:#0a0a0a;--text:#0f172a;--muted:#64748b;--muted-2:#94a3b8;
            --border:#e2e8f0;--surface:#f8fafc;
        }
        *{margin:0;padding:0;box-sizing:border-box;}
        html{scroll-behavior:smooth;}
        body{font-family:'Nunito Sans',sans-serif;background:#fff;color:var(--text);overflow-x:hidden;}
        h1,h2,h3,h4,h5{font-family:'Rubik',sans-serif;}
        ::-webkit-scrollbar{width:5px;}::-webkit-scrollbar-thumb{background:var(--green);border-radius:3px;}

        /* ── NAVBAR ── */
        .front-nav{
            position:fixed;top:0;left:0;right:0;
            z-index:200;
            display:flex;align-items:center;justify-content:space-between;
            padding:0 32px;height:64px;
            background:rgba(255,255,255,.97);backdrop-filter:blur(20px);
            border-bottom:1px solid rgba(0,0,0,.07);
            box-shadow:0 2px 16px rgba(0,0,0,.06);transition:box-shadow .3s,background .3s;
        }
        .front-nav.scrolled{box-shadow:0 4px 24px rgba(0,0,0,.1);background:rgba(255,255,255,1);}
        .fn-nav-inner{
            max-width:1200px;width:100%;margin:0 auto;
            display:flex;align-items:center;justify-content:space-between;
        }
        /* Logo */
        .fn-logo{
            font-family:'Rubik',sans-serif;font-weight:900;font-size:1.12rem;
            color:var(--text);text-decoration:none;
            display:flex;align-items:center;gap:10px;letter-spacing:-.3px;
        }
        .fn-logo-ico{
            width:36px;height:36px;border-radius:10px;
            background:linear-gradient(135deg,#16a34a 0%,#22c55e 100%);
            display:flex;align-items:center;justify-content:center;
            box-shadow:0 2px 8px rgba(22,163,74,.35);flex-shrink:0;
        }
        .fn-logo-text .anti{color:var(--text);}
        .fn-logo-text .gaspi{color:var(--green);}
        .fn-logo-text .ci{color:var(--text);opacity:.5;font-size:.95rem;}
        /* Links */
        .fn-links{display:flex;gap:0;list-style:none;align-items:center;}
        .fn-links li{position:relative;}
        .fn-links a{
            text-decoration:none;color:var(--muted);font-size:.86rem;font-weight:600;
            padding:8px 14px;border-radius:8px;transition:all .2s;
            display:flex;align-items:center;gap:5px;
        }
        .fn-links a:hover{color:var(--text);background:var(--green-50);}
        .fn-links a.active{color:var(--green);font-weight:700;}
        .fn-links a.active::after{
            content:'';position:absolute;bottom:-4px;left:50%;transform:translateX(-50%);
            width:20px;height:3px;background:var(--green);border-radius:3px;
        }
        /* Separateur vertical */
        .fn-sep{width:1px;height:20px;background:var(--border);margin:0 8px;}
        /* Actions */
        .fn-actions{display:flex;gap:8px;align-items:center;}
        .btn-ghost{
            background:none;border:none;color:var(--muted);font-weight:600;font-size:.85rem;
            padding:8px 16px;border-radius:8px;cursor:pointer;transition:all .2s;
            font-family:'Nunito Sans',sans-serif;text-decoration:none;display:inline-block;
        }
        .btn-ghost:hover{background:var(--surface);color:var(--text);}
        .btn-dark{
            background:var(--green);color:#fff;border:none;padding:9px 20px;
            border-radius:8px;font-size:.85rem;font-weight:700;cursor:pointer;
            transition:all .2s;font-family:'Nunito Sans',sans-serif;
            text-decoration:none;display:inline-flex;align-items:center;gap:7px;
            box-shadow:0 2px 8px rgba(22,163,74,.3);
        }
        .btn-dark:hover{background:var(--green-dark);transform:translateY(-1px);color:#fff;box-shadow:0 4px 14px rgba(22,163,74,.4);}
        .btn-green{background:var(--green);color:#fff;border:none;padding:9px 20px;
            border-radius:8px;font-size:.85rem;font-weight:700;cursor:pointer;
            transition:all .2s;font-family:'Nunito Sans',sans-serif;
            text-decoration:none;display:inline-flex;align-items:center;gap:6px;}
        .btn-green:hover{background:var(--green-dark);transform:translateY(-1px);color:#fff;}
        /* Notification bell */
        .notif-btn{
            position:relative;width:38px;height:38px;border-radius:8px;
            background:var(--surface);border:1px solid var(--border);
            display:flex;align-items:center;justify-content:center;
            cursor:pointer;color:var(--muted);font-size:.85rem;transition:all .2s;
            text-decoration:none;
        }
        .notif-btn:hover{background:var(--green-50);color:var(--green);border-color:var(--green-100);}
        .notif-badge{
            position:absolute;top:-4px;right:-4px;background:#ef4444;color:#fff;
            font-size:.5rem;font-weight:800;border-radius:50%;width:16px;height:16px;
            display:flex;align-items:center;justify-content:center;border:2px solid #fff;
        }
        /* Bande verte décorative en haut */
        .front-nav{border-top:3px solid var(--green)!important;}

        /* ── PAGE HERO (mini) ── */
        .page-hero{
            background:linear-gradient(135deg,#052e16 0%,#0d3d1f 50%,#0f5132 100%);
            padding:120px 48px 64px;text-align:center;position:relative;overflow:hidden;
        }
        .page-hero-grid{position:absolute;inset:0;
            background-image:linear-gradient(rgba(255,255,255,.025) 1px,transparent 1px),
                linear-gradient(90deg,rgba(255,255,255,.025) 1px,transparent 1px);
            background-size:52px 52px;pointer-events:none;}
        .page-hero-glow{position:absolute;top:-100px;right:-100px;width:400px;height:400px;
            border-radius:50%;background:radial-gradient(circle,rgba(34,197,94,.12) 0%,transparent 70%);
            pointer-events:none;}
        .page-hero-inner{position:relative;z-index:2;max-width:680px;margin:0 auto;}
        .page-hero-tag{display:inline-flex;align-items:center;gap:7px;
            background:rgba(34,197,94,.12);border:1px solid rgba(34,197,94,.25);
            color:#4ade80;font-size:.7rem;font-weight:700;padding:6px 14px;
            border-radius:50px;margin-bottom:18px;letter-spacing:.6px;text-transform:uppercase;}
        .page-hero h1{font-family:'Rubik',sans-serif;font-size:clamp(2rem,4vw,3rem);
            font-weight:900;color:#fff;letter-spacing:-1.5px;line-height:1.1;margin-bottom:12px;}
        .page-hero h1 span{background:linear-gradient(135deg,#4ade80,#22c55e);
            -webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;}
        .page-hero p{font-size:1rem;color:rgba(255,255,255,.66);line-height:1.68;max-width:520px;margin:0 auto;}
        .page-hero-wave{position:absolute;bottom:-1px;left:0;right:0;pointer-events:none;}

        /* ── COMMON BUTTONS ── */
        .btn-orange{background:var(--orange);color:#fff;padding:13px 28px;border-radius:50px;
            font-family:'Rubik',sans-serif;font-weight:700;font-size:.95rem;text-decoration:none;
            display:inline-flex;align-items:center;gap:7px;transition:all .25s;
            box-shadow:0 4px 18px rgba(249,115,22,.35);border:none;cursor:pointer;}
        .btn-orange:hover{background:var(--orange-dark);transform:translateY(-2px);
            box-shadow:0 8px 28px rgba(249,115,22,.45);color:#fff;}
        .btn-outline-green{background:transparent;color:var(--green);padding:13px 28px;
            border-radius:50px;font-weight:700;font-size:.95rem;text-decoration:none;
            display:inline-flex;align-items:center;gap:7px;transition:all .25s;
            border:1.5px solid var(--green-100);font-family:'Rubik',sans-serif;}
        .btn-outline-green:hover{background:var(--green-50);border-color:var(--green);color:var(--green);}

        /* ── SECTION HEADERS ── */
        .sec-tag{display:inline-flex;align-items:center;gap:7px;font-size:.7rem;font-weight:700;
            letter-spacing:2px;color:var(--green);text-transform:uppercase;margin-bottom:10px;}
        .sec-line{width:20px;height:2px;background:var(--green);border-radius:2px;}
        .sec-title{font-family:'Rubik',sans-serif;font-size:clamp(1.7rem,3vw,2.5rem);
            font-weight:800;letter-spacing:-1px;line-height:1.15;color:var(--text);}
        .sec-title .hl{color:var(--green);}
        .sec-sub{font-size:.95rem;color:var(--muted);line-height:1.7;margin-top:10px;max-width:520px;}

        /* ── FOOTER ── */
        .front-footer{background:#0a0a0a;color:#6b7280;padding:56px 48px 32px;}
        .ff-grid{max-width:1100px;margin:0 auto;
            display:grid;grid-template-columns:2fr 1fr 1fr 1.5fr;gap:40px;
            padding-bottom:40px;border-bottom:1px solid rgba(255,255,255,.07);}
        .ff-logo{font-family:'Rubik',sans-serif;font-weight:800;font-size:1.1rem;color:#22c55e;
            margin-bottom:10px;display:flex;align-items:center;gap:7px;}
        .ff-tag{font-size:.82rem;color:#6b7280;line-height:1.62;max-width:240px;margin-bottom:16px;}
        .ff-social{display:flex;gap:8px;}
        .ffsoc{width:32px;height:32px;border-radius:8px;background:rgba(255,255,255,.05);
            border:1px solid rgba(255,255,255,.08);display:flex;align-items:center;justify-content:center;
            color:#9ca3af;font-size:.8rem;transition:all .2s;cursor:pointer;text-decoration:none;}
        .ffsoc:hover{background:var(--green);border-color:var(--green);color:#fff;}
        .ff-col h5{font-family:'Rubik',sans-serif;font-size:.82rem;font-weight:700;color:#fff;
            margin-bottom:13px;letter-spacing:.3px;}
        .ff-links{list-style:none;display:flex;flex-direction:column;gap:8px;}
        .ff-links a{color:#6b7280;text-decoration:none;font-size:.8rem;transition:color .2s;}
        .ff-links a:hover{color:#fff;}
        .ff-nl h5{font-family:'Rubik',sans-serif;font-size:.82rem;font-weight:700;color:#fff;margin-bottom:10px;}
        .ff-nl p{font-size:.78rem;color:#6b7280;line-height:1.6;margin-bottom:10px;}
        .nl-row{display:flex;gap:6px;}
        .nl-inp{flex:1;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1);
            border-radius:50px;padding:9px 13px;color:#fff;font-size:.78rem;
            font-family:'Nunito Sans',sans-serif;outline:none;transition:border-color .2s;}
        .nl-inp::placeholder{color:#4b5563;}
        .nl-inp:focus{border-color:var(--green);}
        .nl-btn{background:var(--green);color:#fff;border:none;padding:9px 16px;border-radius:50px;
            cursor:pointer;font-size:.76rem;font-weight:700;transition:all .2s;white-space:nowrap;}
        .nl-btn:hover{background:var(--green-dark);}
        .ff-bot{max-width:1100px;margin:0 auto;display:flex;justify-content:space-between;
            align-items:center;padding-top:26px;flex-wrap:wrap;gap:8px;}
        .ff-copy{font-size:.76rem;}
        .ff-legal{display:flex;gap:16px;}
        .ff-legal a{font-size:.76rem;color:#6b7280;text-decoration:none;}
        .ff-legal a:hover{color:#fff;}

        /* ── REVEAL ── */
        .reveal{opacity:0;transform:translateY(26px);transition:all .6s ease;}
        .reveal.visible{opacity:1;transform:translateY(0);}
        .reveal-left{opacity:0;transform:translateX(-30px);transition:all .6s ease;}
        .reveal-left.visible{opacity:1;transform:translateX(0);}
        .reveal-right{opacity:0;transform:translateX(30px);transition:all .6s ease;}
        .reveal-right.visible{opacity:1;transform:translateX(0);}

        /* ── MOBILE ── */
        @media(max-width:900px){
            .fn-links{display:none;}
            .fn-sep{display:none;}
        }
        @media(max-width:768px){
            .front-nav{padding:0 16px;height:56px;}
            .fn-logo{font-size:.92rem;}
            .fn-logo-ico{width:30px;height:30px;border-radius:8px;}
            .fn-actions .btn-ghost{display:none;}
            .fn-actions .btn-dark{padding:7px 14px;font-size:.78rem;}
            .page-hero{padding:90px 16px 40px;}
            .front-footer{padding:36px 16px 20px;}
            .ff-grid{grid-template-columns:1fr;}
            .ff-bot{flex-direction:column;text-align:center;gap:10px;}
        }
        @media(max-width:480px){
            .fn-actions .notif-btn{width:34px;height:34px;font-size:.8rem;}
            .fn-actions .btn-dark{padding:6px 12px;font-size:.75rem;}
            .fc-popup{width:calc(100vw - 24px);right:0;bottom:70px;}
            .fc-wrap{right:12px;bottom:16px;}
            .fc-btn{width:50px;height:50px;font-size:1.2rem;}
        }
    </style>
</head>
<body>

<nav class="front-nav" id="frontNav">
    <div class="fn-nav-inner">

        {{-- Logo --}}
        <a class="fn-logo" href="{{ route('home') }}">
            <div class="fn-logo-ico">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M21 3C21 3 14 3 10 8C7.5 11 9 15 9 15C12 12 15 12 17 8C17 8 18 12 17 16" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M3.82 21C5.9 16.17 8 10 17 8" stroke="white" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </div>
            <span class="fn-logo-text">
                <span class="anti">Anti</span><span class="gaspi">Gaspi</span><span class="ci">CI</span>
            </span>
        </a>

        {{-- Liens --}}
        <ul class="fn-links">
            <li><a href="{{ route('comment-ca-marche') }}" class="{{ request()->is('comment-ca-marche') ? 'active':'' }}">Fonctionnement</a></li>
            <li><a href="{{ route('annonces.index') }}" class="{{ request()->is('annonces*') ? 'active':'' }}">Annonces</a></li>
            <li><a href="{{ route('blog') }}" class="{{ request()->is('blog') ? 'active':'' }}">Blog</a></li>
            <li><a href="{{ route('contact') }}" class="{{ request()->is('contact') ? 'active':'' }}">Contact</a></li>
        </ul>

        {{-- Actions --}}
        <div class="fn-actions">
            @auth
                @php $unread = Auth::user()->unreadNotifications->count(); @endphp
                <a href="{{ route('notifications.index') }}" class="notif-btn" title="Notifications">
                    <i class="fas fa-bell"></i>
                    @if($unread > 0)<span class="notif-badge">{{ $unread > 9 ? '9+' : $unread }}</span>@endif
                </a>
                <div class="fn-sep"></div>
                <a href="{{ route('dashboard') }}" class="btn-dark">
                    <i class="fas fa-th-large"></i> Mon espace
                </a>
            @else
                <a href="{{ route('connexion') }}" class="btn-ghost">Connexion</a>
                <a href="{{ route('inscription') }}" class="btn-dark">
                    <i class="fas fa-leaf"></i> S'inscrire
                </a>
            @endauth
        </div>

    </div>
</nav>

@yield('content')

<footer class="front-footer">
    <div class="ff-grid">
        <div>
            <div class="ff-logo">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M21 3C21 3 14 3 10 8C7.5 11 9 15 9 15C12 12 15 12 17 8C17 8 18 12 17 16" stroke="#22c55e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M3.82 21C5.9 16.17 8 10 17 8" stroke="#22c55e" stroke-width="2" stroke-linecap="round"/>
                </svg>
                AntiGaspiCI
            </div>
            <p class="ff-tag">La plateforme collaborative de réduction du gaspillage alimentaire en Côte d'Ivoire.</p>
            <div class="ff-social">
                <a href="#" class="ffsoc"><i class="fab fa-facebook-f"></i></a>
                <a href="#" class="ffsoc"><i class="fab fa-instagram"></i></a>
                <a href="#" class="ffsoc"><i class="fab fa-twitter"></i></a>
                <a href="#" class="ffsoc"><i class="fab fa-whatsapp"></i></a>
            </div>
        </div>
        <div class="ff-col">
            <h5>Plateforme</h5>
            <ul class="ff-links">
                <li><a href="{{ route('annonces.index') }}">Annonces</a></li>
                <li><a href="{{ route('comment-ca-marche') }}">Fonctionnement</a></li>
                <li><a href="{{ route('blog') }}">Blog</a></li>
                <li><a href="{{ route('contact') }}">Contact</a></li>
            </ul>
        </div>
        <div class="ff-col">
            <h5>Mon compte</h5>
            <ul class="ff-links">
                <li><a href="{{ route('connexion') }}">Connexion</a></li>
                <li><a href="{{ route('inscription') }}">Inscription</a></li>
                @auth
                <li><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li><a href="{{ route('annonces.create') }}">Publier</a></li>
                @endauth
            </ul>
        </div>
        <div class="ff-nl">
            <h5>Alertes & Actus</h5>
            <p>Recevez les meilleures offres près de chez vous.</p>
            <div class="nl-row">
                <input type="email" class="nl-inp" placeholder="votre@email.com">
                <button class="nl-btn">OK</button>
            </div>
        </div>
    </div>
    <div class="ff-bot">
        <div class="ff-copy">© {{ date('Y') }} AntiGaspiCI — Côte d'Ivoire. Tous droits réservés.</div>
        <div class="ff-legal">
            <a href="#">Mentions légales</a>
            <a href="#">Confidentialité</a>
            <a href="#">CGU</a>
        </div>
    </div>
</footer>

{{-- ── FLOATING CART ── --}}
@auth
@php
    $floatCartItems = \App\Models\CartItem::with(['annonce.photoPrincipale'])
        ->where('user_id', Auth::id())->get();
    $floatCartCount = $floatCartItems->count();
    $floatCartTotal = $floatCartItems->sum(fn($i) =>
        $i->annonce->type_offre === 'don' ? 0 : (float)$i->annonce->prix * (float)$i->quantite
    );
@endphp
<style>
.fc-wrap{position:fixed;bottom:28px;right:28px;z-index:9990;display:flex;flex-direction:column;align-items:flex-end;gap:12px;}
.fc-btn{width:58px;height:58px;border-radius:50%;background:linear-gradient(135deg,#16a34a,#15803d);
    color:#fff;border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;
    font-size:1.35rem;box-shadow:0 6px 24px rgba(22,163,74,.45);transition:all .25s;position:relative;}
.fc-btn:hover{transform:scale(1.1);box-shadow:0 10px 32px rgba(22,163,74,.55);}
.fc-badge{position:absolute;top:-4px;right:-4px;background:#ef4444;color:#fff;font-size:.65rem;
    font-weight:800;width:20px;height:20px;border-radius:50%;display:flex;align-items:center;
    justify-content:center;border:2px solid #fff;animation:fcPop .3s ease;}
@keyframes fcPop{0%{transform:scale(0)}100%{transform:scale(1)}}
.fc-popup{width:300px;background:#fff;border-radius:18px;box-shadow:0 16px 48px rgba(0,0,0,.16);
    overflow:hidden;display:none;flex-direction:column;transform-origin:bottom right;animation:fcSlide .2s ease;}
@keyframes fcSlide{0%{opacity:0;transform:scale(.9) translateY(10px)}100%{opacity:1;transform:scale(1) translateY(0)}}
.fc-popup.open{display:flex;}
.fc-head{background:linear-gradient(135deg,#16a34a,#15803d);padding:14px 16px;display:flex;justify-content:space-between;align-items:center;}
.fc-head h6{color:#fff;font-weight:800;font-size:.88rem;margin:0;display:flex;align-items:center;gap:7px;}
.fc-close{background:rgba(255,255,255,.2);border:none;color:#fff;width:26px;height:26px;border-radius:50%;cursor:pointer;font-size:.85rem;display:flex;align-items:center;justify-content:center;}
.fc-close:hover{background:rgba(255,255,255,.35);}
.fc-body{max-height:240px;overflow-y:auto;padding:10px 14px;}
.fc-item{display:flex;align-items:center;gap:10px;padding:8px 0;border-bottom:1px solid #f1f5f9;}
.fc-item:last-child{border-bottom:none;}
.fc-item-img{width:38px;height:38px;border-radius:8px;object-fit:cover;flex-shrink:0;background:#f1f5f9;display:flex;align-items:center;justify-content:center;}
.fc-item-info{flex:1;min-width:0;}
.fc-item-name{font-size:.78rem;font-weight:700;color:#1e293b;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
.fc-item-qty{font-size:.72rem;color:#64748b;}
.fc-item-price{font-size:.78rem;font-weight:800;color:#16a34a;white-space:nowrap;}
.fc-empty{text-align:center;padding:24px 0;color:#94a3b8;font-size:.82rem;}
.fc-empty i{font-size:1.8rem;display:block;margin-bottom:8px;color:#cbd5e1;}
.fc-foot{padding:12px 14px;border-top:1px solid #f1f5f9;background:#f8fafc;}
.fc-total{display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;}
.fc-total span{font-size:.8rem;color:#64748b;font-weight:600;}
.fc-total strong{font-size:.95rem;font-weight:900;color:#16a34a;}
.fc-actions{display:flex;gap:8px;}
.fc-btn-voir{flex:1;background:linear-gradient(135deg,#16a34a,#15803d);color:#fff;border:none;
    padding:9px;border-radius:50px;font-size:.78rem;font-weight:700;text-align:center;
    text-decoration:none;display:flex;align-items:center;justify-content:center;gap:5px;transition:all .2s;}
.fc-btn-voir:hover{opacity:.9;color:#fff;}
.fc-btn-vider{background:#fee2e2;color:#ef4444;border:none;padding:9px 13px;border-radius:50px;
    font-size:.75rem;font-weight:700;cursor:pointer;transition:all .2s;white-space:nowrap;}
.fc-btn-vider:hover{background:#fecaca;}
</style>

<div class="fc-wrap">
    <div class="fc-popup" id="fcPopup">
        <div class="fc-head">
            <h6><i class="fas fa-shopping-cart"></i> Mon panier
                @if($floatCartCount > 0)
                <span style="background:rgba(255,255,255,.25);padding:1px 8px;border-radius:50px;font-size:.72rem;">
                    {{ $floatCartCount }} article{{ $floatCartCount > 1 ? 's' : '' }}
                </span>
                @endif
            </h6>
            <button class="fc-close" onclick="toggleCart()"><i class="fas fa-times"></i></button>
        </div>
        <div class="fc-body">
            @if($floatCartItems->isEmpty())
                <div class="fc-empty"><i class="fas fa-shopping-cart"></i>Votre panier est vide</div>
            @else
                @foreach($floatCartItems as $ci)
                <div class="fc-item">
                    @if($ci->annonce->photoPrincipale)
                        <img src="{{ $ci->annonce->photoPrincipale->url }}" class="fc-item-img" alt="" onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                        <div class="fc-item-img" style="display:none"><i class="fas fa-image" style="color:#cbd5e1;"></i></div>
                    @else
                        <div class="fc-item-img"><i class="fas fa-image" style="color:#cbd5e1;"></i></div>
                    @endif
                    <div class="fc-item-info">
                        <div class="fc-item-name">{{ $ci->annonce->titre }}</div>
                        <div class="fc-item-qty">{{ $ci->quantite }} {{ $ci->annonce->unite }}</div>
                    </div>
                    <div class="fc-item-price">
                        @if($ci->annonce->type_offre === 'don') Gratuit
                        @else {{ number_format((float)$ci->annonce->prix * (float)$ci->quantite, 0, ',', ' ') }} F
                        @endif
                    </div>
                </div>
                @endforeach
            @endif
        </div>
        @if($floatCartItems->isNotEmpty())
        <div class="fc-foot">
            <div class="fc-total">
                <span>Total estimé</span>
                <strong>{{ number_format($floatCartTotal, 0, ',', ' ') }} FCFA</strong>
            </div>
            <div class="fc-actions">
                <a href="{{ route('cart.index') }}" class="fc-btn-voir"><i class="fas fa-eye"></i> Voir le panier</a>
                <form action="{{ route('cart.vider') }}" method="POST" style="display:inline">
                    @csrf @method('DELETE')
                    <button type="submit" class="fc-btn-vider" onclick="return confirm('Vider le panier ?')">
                        <i class="fas fa-trash-alt"></i> Vider
                    </button>
                </form>
            </div>
        </div>
        @endif
    </div>

    <button class="fc-btn" onclick="toggleCart()" title="Mon panier">
        <i class="fas fa-shopping-cart"></i>
        @if($floatCartCount > 0)
            <span class="fc-badge">{{ $floatCartCount > 9 ? '9+' : $floatCartCount }}</span>
        @endif
    </button>
</div>

<script>
function toggleCart(){document.getElementById('fcPopup').classList.toggle('open');}
document.addEventListener('click',function(e){
    const w=document.querySelector('.fc-wrap');
    if(w&&!w.contains(e.target))document.getElementById('fcPopup').classList.remove('open');
});
</script>
@endauth

<script>
window.addEventListener('scroll',()=>{
    document.getElementById('frontNav').classList.toggle('scrolled',window.scrollY>50);
});
const revObs=new IntersectionObserver(entries=>{
    entries.forEach(e=>{if(e.isIntersecting)e.target.classList.add('visible');});
},{threshold:.08,rootMargin:'0px 0px -30px 0px'});
document.querySelectorAll('.reveal,.reveal-left,.reveal-right').forEach(el=>revObs.observe(el));
</script>
@stack('scripts')
</body>
</html>
