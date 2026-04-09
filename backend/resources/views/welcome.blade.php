<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>AntiGaspiCI — Plateforme Anti-Gaspillage Alimentaire en Côte d'Ivoire</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Plateforme collaborative de réduction du gaspillage alimentaire en Côte d'Ivoire">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css"/>
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: #fff; color: #111; overflow-x: hidden; }

        /* ── NAVBAR ── */
        .navbar-custom {
            position: fixed; top: 0; left: 0; right: 0; z-index: 100;
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 48px; height: 64px;
            background: rgba(255,255,255,0.92);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid #f0f0f0;
        }
        .navbar-logo { font-weight: 800; font-size: 1.2rem; color: #16a34a; text-decoration: none; display: flex; align-items: center; gap: 8px; }
        .navbar-logo span { color: #111; }
        .navbar-links { display: flex; gap: 32px; list-style: none; }
        .navbar-links a { text-decoration: none; color: #555; font-size: 0.9rem; font-weight: 500; transition: color .2s; }
        .navbar-links a:hover { color: #111; }
        .navbar-actions { display: flex; gap: 12px; align-items: center; }
        .btn-login-nav {
            background: #111; color: #fff; border: none;
            padding: 10px 28px; border-radius: 50px;
            font-size: 0.875rem; font-weight: 700; cursor: pointer;
            transition: background .2s, transform .15s; font-family: 'Inter', sans-serif;
        }
        .btn-login-nav:hover { background: #333; transform: translateY(-1px); }

        /* ── AUTH MODAL ── */
        .auth-overlay {
            display: none; position: fixed; inset: 0; z-index: 9999;
            background: rgba(0,0,0,.55); backdrop-filter: blur(6px);
            align-items: center; justify-content: center; padding: 20px;
        }
        .auth-overlay.open { display: flex; animation: fadeIn .2s ease; }
        @keyframes fadeIn { from { opacity:0 } to { opacity:1 } }

        .auth-modal {
            background: #fff; border-radius: 24px;
            width: 100%; max-width: 780px;
            display: grid; grid-template-columns: 1fr 1fr;
            overflow: hidden;
            box-shadow: 0 32px 80px rgba(0,0,0,.25);
            animation: slideUp .25s ease;
        }
        @keyframes slideUp { from { transform:translateY(20px);opacity:0 } to { transform:translateY(0);opacity:1 } }

        /* ── LEFT PANEL ── */
        .auth-left { padding: 44px 40px; display: flex; flex-direction: column; justify-content: center; }
        .auth-left h2 { font-size: 1.9rem; font-weight: 800; margin-bottom: 6px; letter-spacing: -.5px; }
        .auth-left .sub { font-size: 0.875rem; color: #888; margin-bottom: 28px; line-height: 1.5; }
        .auth-left .sub strong { color: #16a34a; }

        .auth-input-wrap { position: relative; margin-bottom: 14px; }
        .auth-input-wrap input {
            width: 100%; padding: 14px 18px;
            border: 1.5px solid #e5e5e5; border-radius: 50px;
            font-size: 0.9rem; outline: none; font-family: 'Inter',sans-serif;
            transition: border-color .2s;
        }
        .auth-input-wrap input:focus { border-color: #16a34a; }
        .auth-input-wrap .eye {
            position: absolute; right: 18px; top: 50%; transform: translateY(-50%);
            color: #bbb; cursor: pointer; font-size: .85rem;
        }
        .auth-forgot { text-align: right; font-size: 0.8rem; color: #888; margin-bottom: 20px; cursor: pointer; text-decoration: none; }
        .auth-forgot:hover { color: #16a34a; }

        .btn-auth-submit {
            width: 100%; background: #111; color: #fff;
            border: none; padding: 15px; border-radius: 50px;
            font-size: 1rem; font-weight: 700; cursor: pointer;
            font-family: 'Inter',sans-serif;
            transition: background .2s, transform .15s;
        }
        .btn-auth-submit:hover { background: #333; transform: translateY(-1px); }

        .auth-divider { display: flex; align-items: center; gap: 12px; margin: 20px 0; }
        .auth-divider span { font-size: 0.78rem; color: #bbb; white-space: nowrap; }
        .auth-divider::before, .auth-divider::after { content:''; flex:1; height:1px; background:#eee; }

        .social-btns { display: flex; gap: 12px; justify-content: center; margin-bottom: 20px; }
        .btn-social {
            width: 46px; height: 46px; border-radius: 50%;
            background: #111; color: #fff; border: none;
            display: flex; align-items: center; justify-content: center;
            font-size: 1rem; cursor: pointer; transition: transform .15s, background .2s;
        }
        .btn-social:hover { transform: scale(1.1); background: #16a34a; }
        .btn-social.apple  { background: #111; }
        .btn-social.google { background: #db4437; }
        .btn-social.fb     { background: #1877f2; }

        .auth-switch { font-size: 0.83rem; color: #888; text-align: center; }
        .auth-switch a { color: #16a34a; font-weight: 600; text-decoration: none; }
        .auth-switch a:hover { text-decoration: underline; }

        /* error message */
        .auth-error { font-size: .8rem; color: #dc2626; margin-bottom: 10px; display: none; padding: 10px 14px; background: #fef2f2; border-radius: 10px; border: 1px solid #fecaca; }
        .auth-error.show { display: block; }

        /* ── RIGHT PANEL ── */
        .auth-right {
            background: #f0fdf4; position: relative;
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            padding: 40px 32px; text-align: center; overflow: hidden;
        }
        .auth-right::before {
            content: ''; position: absolute; top: -60px; right: -60px;
            width: 220px; height: 220px; border-radius: 50%;
            background: rgba(22,163,74,.08);
        }
        .auth-right::after {
            content: ''; position: absolute; bottom: -40px; left: -40px;
            width: 160px; height: 160px; border-radius: 50%;
            background: rgba(22,163,74,.06);
        }
        .auth-illus { font-size: 5.5rem; margin-bottom: 16px; animation: floatAnim 3s ease-in-out infinite; }
        .auth-right-title { font-size: 1.15rem; font-weight: 700; color: #111; line-height: 1.4; margin-bottom: 6px; }
        .auth-right-title strong { color: #16a34a; }
        .auth-right-sub { font-size: 0.8rem; color: #666; max-width: 200px; line-height: 1.6; margin-bottom: 24px; }

        .mini-card {
            background: #fff; border-radius: 16px; padding: 14px 18px;
            text-align: left; box-shadow: 0 4px 20px rgba(0,0,0,.1);
            display: flex; align-items: center; gap: 12px;
            margin-bottom: 12px; width: 100%; max-width: 220px;
            animation: floatAnim 4s ease-in-out infinite;
        }
        .mini-card-icon { font-size: 1.6rem; }
        .mini-card-label { font-size: 0.75rem; font-weight: 700; color: #111; }
        .mini-card-sub   { font-size: 0.7rem; color: #888; }
        .mini-card-prog  { height: 4px; background: #e5e7eb; border-radius: 4px; margin-top: 6px; overflow: hidden; }
        .mini-card-fill  { height: 100%; background: #16a34a; border-radius: 4px; width: 72%; }

        .auth-avatars { display: flex; gap: -8px; margin-top: 16px; }
        .auth-av {
            width: 36px; height: 36px; border-radius: 50%;
            border: 2.5px solid #fff; font-size: 1.1rem;
            background: #dcfce7; display: flex; align-items: center; justify-content: center;
            margin-left: -8px;
        }
        .auth-av:first-child { margin-left: 0; }

        /* close btn */
        .auth-close {
            position: absolute; top: 14px; right: 14px; z-index: 10;
            width: 32px; height: 32px; border-radius: 50%;
            background: #f5f5f5; border: none; cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            font-size: 1rem; color: #666; transition: background .2s;
        }
        .auth-close:hover { background: #eee; }

        /* register mode */
        .register-only { display: none; }
        .auth-modal.register-mode .register-only { display: block; }
        .auth-modal.register-mode .login-only { display: none; }

        @media (max-width: 640px) {
            .auth-modal { grid-template-columns: 1fr; }
            .auth-right { display: none; }
        }

        /* ── HERO ── */
        .hero {
            min-height: 100vh;
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            text-align: center; padding: 80px 24px 40px;
            position: relative; overflow: hidden;
        }
        .hero-badge {
            display: inline-flex; align-items: center; gap: 8px;
            background: #f0fdf4; border: 1px solid #bbf7d0;
            color: #16a34a; font-size: 0.78rem; font-weight: 600;
            padding: 6px 14px; border-radius: 50px; margin-bottom: 28px;
            letter-spacing: .5px;
        }
        .hero-title {
            font-size: clamp(2.8rem, 7vw, 5.5rem);
            font-weight: 900; line-height: 1.08;
            letter-spacing: -2px; max-width: 820px;
            color: #0a0a0a;
        }
        .hero-title .highlight { color: #16a34a; }
        .hero-subtitle {
            margin-top: 20px; max-width: 520px;
            font-size: 1.05rem; color: #666; line-height: 1.65; font-weight: 400;
        }
        .hero-cta { margin-top: 36px; display: flex; gap: 14px; justify-content: center; flex-wrap: wrap; }
        .btn-primary-hero {
            background: #f97316; color: #fff;
            padding: 15px 36px; border-radius: 50px;
            font-weight: 700; font-size: 1rem;
            text-decoration: none; transition: transform .2s, box-shadow .2s;
            box-shadow: 0 4px 20px rgba(249,115,22,.35);
        }
        .btn-primary-hero:hover { transform: translateY(-2px); box-shadow: 0 8px 28px rgba(249,115,22,.45); color: #fff; }
        .btn-secondary-hero {
            background: #fff; color: #111;
            padding: 15px 36px; border-radius: 50px;
            font-weight: 600; font-size: 1rem;
            text-decoration: none; border: 1.5px solid #e5e5e5;
            transition: border-color .2s, transform .2s;
        }
        .btn-secondary-hero:hover { border-color: #aaa; transform: translateY(-2px); color: #111; }

        /* ── FLOATING ICONS VISUAL ── */
        .hero-visual {
            position: relative; width: 100%; max-width: 700px;
            height: 340px; margin-top: 56px;
        }
        .hero-visual svg.lines {
            position: absolute; top: 0; left: 0; width: 100%; height: 100%;
            z-index: 0; pointer-events: none;
        }
        .float-item {
            position: absolute; z-index: 2;
            display: flex; align-items: center; justify-content: center;
            border-radius: 22px; font-size: 1.6rem;
            box-shadow: 0 8px 32px rgba(0,0,0,.1);
            animation: floatAnim 4s ease-in-out infinite;
        }
        .float-item.center {
            width: 90px; height: 90px;
            background: linear-gradient(135deg, #16a34a, #22c55e);
            border-radius: 28px;
            top: 50%; left: 50%; transform: translate(-50%,-50%);
            font-size: 2.4rem; animation: none;
            box-shadow: 0 12px 40px rgba(22,163,74,.4);
        }
        /* positions */
        .fi-1  { width:64px;height:64px; background:#fef9c3; top:12%;  left:8%;   animation-delay:0s;   }
        .fi-2  { width:64px;height:64px; background:#dbeafe; top:62%;  left:5%;   animation-delay:.6s;  }
        .fi-3  { width:64px;height:64px; background:#ffe4e6; top:10%;  right:10%; animation-delay:1.2s; }
        .fi-4  { width:64px;height:64px; background:#f3e8ff; top:60%;  right:6%;  animation-delay:1.8s; }
        .fi-5  { width:64px;height:64px; background:#ffedd5; top:5%;   left:40%;  animation-delay:2.4s; }
        .fi-6  { width:64px;height:64px; background:#dcfce7; top:80%;  left:38%;  animation-delay:3s;   }
        /* avatar photos */
        .avatar-float {
            position: absolute; z-index: 3;
            width: 56px; height: 56px; border-radius: 50%;
            border: 3px solid #fff;
            box-shadow: 0 4px 16px rgba(0,0,0,.15);
            overflow: hidden; animation: floatAnim 5s ease-in-out infinite;
            background: #e5e7eb;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.4rem;
        }
        .av-1 { top:25%; left:0%;  animation-delay:.3s; }
        .av-2 { top:38%; right:0%; animation-delay:1.5s; }

        @keyframes floatAnim {
            0%,100% { transform: translateY(0); }
            50%      { transform: translateY(-10px); }
        }

        /* ── STATS ── */
        .stats-bar {
            background: #0a0a0a; color: #fff;
            padding: 32px 48px;
            display: flex; justify-content: center; gap: 64px; flex-wrap: wrap;
        }
        .stat-item { text-align: center; }
        .stat-item .num { font-size: 2.2rem; font-weight: 800; color: #22c55e; line-height: 1; }
        .stat-item .lbl { font-size: 0.82rem; color: #aaa; margin-top: 4px; font-weight: 400; }

        /* ── HOW IT WORKS ── */
        .section { padding: 80px 48px; max-width: 1100px; margin: 0 auto; }
        .section-label { font-size: 0.78rem; font-weight: 700; letter-spacing: 2px; color: #16a34a; text-transform: uppercase; margin-bottom: 12px; }
        .section-title { font-size: clamp(1.8rem, 4vw, 3rem); font-weight: 800; letter-spacing: -1px; max-width: 600px; line-height: 1.15; }
        .steps-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 24px; margin-top: 48px; }
        .step-card {
            background: #fff; border: 1.5px solid #f0f0f0;
            border-radius: 20px; padding: 28px 24px;
            transition: box-shadow .25s, transform .25s;
        }
        .step-card:hover { box-shadow: 0 12px 40px rgba(0,0,0,.08); transform: translateY(-4px); }
        .step-num {
            width: 44px; height: 44px; border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.4rem; margin-bottom: 16px;
        }
        .step-card h4 { font-size: 1rem; font-weight: 700; margin-bottom: 8px; }
        .step-card p  { font-size: 0.875rem; color: #666; line-height: 1.6; }

        /* ── ANNONCES ── */
        /* ── BOUTIQUE / SHOP ── */
        .shop-section { background: #f9fafb; padding: 80px 0; }
        .shop-inner { max-width: 1200px; margin: 0 auto; padding: 0 48px; }
        .shop-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(260px,1fr)); gap: 24px; margin-top: 40px; }

        .shop-card {
            background: #fff; border-radius: 20px; overflow: hidden;
            border: 1.5px solid #f0f0f0;
            transition: box-shadow .28s, transform .28s;
            display: flex; flex-direction: column;
        }
        .shop-card:hover { box-shadow: 0 16px 48px rgba(0,0,0,.10); transform: translateY(-5px); }

        .shop-img-wrap { position: relative; height: 200px; overflow: hidden; background: #f3f4f6; }
        .shop-img-wrap img { width: 100%; height: 100%; object-fit: cover; }
        .shop-img-emoji { width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; font-size: 4rem; background: linear-gradient(135deg,#f0fdf4,#dcfce7); }
        .shop-type-badge {
            position: absolute; top: 12px; left: 12px;
            font-size: 0.68rem; font-weight: 700; letter-spacing: .8px; text-transform: uppercase;
            padding: 4px 12px; border-radius: 50px; backdrop-filter: blur(8px);
        }
        .type-don      { background: rgba(22,163,74,.15); color: #16a34a; border: 1px solid rgba(22,163,74,.3); }
        .type-vente    { background: rgba(29,78,216,.12); color: #1d4ed8; border: 1px solid rgba(29,78,216,.25); }
        .type-animal   { background: rgba(234,88,12,.12);  color: #ea580c; border: 1px solid rgba(234,88,12,.25); }
        .type-transform{ background: rgba(147,51,234,.12); color: #9333ea; border: 1px solid rgba(147,51,234,.25); }

        .shop-body { padding: 18px; flex: 1; display: flex; flex-direction: column; }
        .shop-title { font-size: 1rem; font-weight: 700; color: #111; margin-bottom: 6px; line-height: 1.3; }
        .shop-seller { display: flex; align-items: center; gap: 7px; margin-bottom: 10px; }
        .shop-seller-avatar { width: 22px; height: 22px; border-radius: 50%; background: linear-gradient(135deg,#16a34a,#22c55e); display: flex; align-items: center; justify-content: center; font-size: .6rem; font-weight: 800; color: #fff; flex-shrink: 0; }
        .shop-seller-name { font-size: 0.78rem; color: #888; }
        .shop-seller-company { font-size: 0.78rem; color: #16a34a; font-weight: 600; }
        .shop-meta { display: flex; gap: 8px; align-items: center; margin-bottom: 12px; flex-wrap: wrap; }
        .shop-meta span { font-size: 0.72rem; color: #aaa; display: flex; align-items: center; gap: 3px; }
        .shop-footer { display: flex; align-items: center; justify-content: space-between; margin-top: auto; padding-top: 12px; border-top: 1px solid #f5f5f5; }
        .shop-price { font-size: 1.15rem; font-weight: 800; color: #111; }
        .shop-price .fcfa { font-size: .72rem; font-weight: 600; color: #888; }
        .shop-price.free { color: #16a34a; }
        .shop-btn { background: #111; color: #fff; border: none; padding: 9px 18px; border-radius: 50px; font-size: .78rem; font-weight: 700; cursor: pointer; transition: background .2s; text-decoration: none; display: inline-block; }
        .shop-btn:hover { background: #16a34a; color: #fff; }

        /* legacy compat */
        .annonce-img { width: 100%; height: 160px; object-fit: cover; background: #e5e7eb; display: flex; align-items: center; justify-content: center; font-size: 3rem; }

        /* ── CATEGORIES ── */
        .categories-section { padding: 80px 48px; max-width: 1100px; margin: 0 auto; }
        .cats-grid { display: flex; gap: 12px; flex-wrap: wrap; margin-top: 32px; }
        .cat-pill {
            background: #f9fafb; border: 1.5px solid #f0f0f0;
            border-radius: 50px; padding: 10px 22px;
            font-size: 0.875rem; font-weight: 600;
            display: flex; align-items: center; gap: 8px;
            text-decoration: none; color: #333;
            transition: all .2s;
        }
        .cat-pill:hover { background: #16a34a; border-color: #16a34a; color: #fff; transform: translateY(-2px); box-shadow: 0 4px 16px rgba(22,163,74,.25); }
        .cat-count { font-size: 0.75rem; color: #999; font-weight: 400; }
        .cat-pill:hover .cat-count { color: rgba(255,255,255,.8); }

        /* ── CTA BAND ── */
        .cta-band {
            background: linear-gradient(135deg, #16a34a 0%, #15803d 100%);
            padding: 80px 48px; text-align: center; color: #fff;
        }
        .cta-band h2 { font-size: clamp(2rem, 4vw, 3.2rem); font-weight: 800; letter-spacing: -1px; max-width: 600px; margin: 0 auto 16px; }
        .cta-band p  { font-size: 1.05rem; opacity: .85; max-width: 440px; margin: 0 auto 32px; }
        .btn-cta-white {
            background: #fff; color: #16a34a;
            padding: 16px 40px; border-radius: 50px;
            font-weight: 700; font-size: 1rem; text-decoration: none;
            transition: transform .2s, box-shadow .2s;
            display: inline-block;
            box-shadow: 0 4px 20px rgba(0,0,0,.15);
        }
        .btn-cta-white:hover { transform: translateY(-3px); box-shadow: 0 8px 32px rgba(0,0,0,.2); color: #16a34a; }

        /* ── FOOTER ── */
        footer {
            background: #0a0a0a; color: #aaa;
            padding: 48px;
            display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;
        }
        footer .logo { font-weight: 800; color: #22c55e; font-size: 1.1rem; }
        footer nav { display: flex; gap: 24px; }
        footer nav a { color: #aaa; text-decoration: none; font-size: 0.85rem; transition: color .2s; }
        footer nav a:hover { color: #fff; }
        footer .copy { font-size: 0.8rem; }

        @media (max-width: 768px) {
            .navbar-custom { padding: 0 20px; }
            .navbar-links { display: none; }
            .hero-visual { height: 260px; }
            .stats-bar { gap: 32px; padding: 24px; }
            .section, .categories-section { padding: 48px 20px; }
            .annonces-inner { padding: 0 20px; }
            .cta-band { padding: 60px 20px; }
            footer { padding: 32px 20px; flex-direction: column; text-align: center; }
        }
    </style>
</head>
<body>

<!-- ── NAVBAR ── -->
<nav class="navbar-custom">
    <a class="navbar-logo" href="{{ route('home') }}">
        🌿 <span>Anti</span>GaspiCI
    </a>
    <ul class="navbar-links">
        <li><a href="{{ route('comment-ca-marche') }}">Fonctionnement</a></li>
        <li><a href="{{ route('annonces.index') }}">Annonces</a></li>
        <li><a href="{{ route('blog') }}">Blog</a></li>
        <li><a href="{{ route('contact') }}">Contact</a></li>
    </ul>
    <div class="navbar-actions">
        @auth
            <a href="{{ route('dashboard') }}" class="btn-login-nav">Dashboard</a>
        @else
            <button class="btn-login-nav" onclick="openAuth('login')">Login</button>
        @endauth
    </div>
</nav>

<!-- ── HERO ── -->
<section class="hero">

    <div class="hero-badge">
        <span>🌍</span> Côte d'Ivoire · Économie Circulaire
    </div>

    <h1 class="hero-title">
        La plateforme<br>
        <span class="highlight">anti-gaspillage</span><br>
        alimentaire
    </h1>

    <p class="hero-subtitle">
        AntiGaspiCI connecte fournisseurs et acheteurs pour transformer les surplus alimentaires en opportunités — dons, ventes à prix réduit, alimentation animale.
    </p>

    <div class="hero-cta">
        <a href="{{ route('inscription') }}" class="btn-primary-hero">
            Commencer gratuitement
        </a>
        <a href="{{ route('annonces.index') }}" class="btn-secondary-hero">
            <i class="fas fa-search me-2"></i>Voir les annonces
        </a>
    </div>

    <!-- Floating Visual -->
    <div class="hero-visual">

        <!-- SVG connecting lines -->
        <svg class="lines" viewBox="0 0 700 340" preserveAspectRatio="xMidYMid meet">
            <line x1="350" y1="170" x2="70"  y2="55"  stroke="#d1fae5" stroke-width="1.5" stroke-dasharray="6,4"/>
            <line x1="350" y1="170" x2="50"  y2="220" stroke="#d1fae5" stroke-width="1.5" stroke-dasharray="6,4"/>
            <line x1="350" y1="170" x2="620" y2="50"  stroke="#d1fae5" stroke-width="1.5" stroke-dasharray="6,4"/>
            <line x1="350" y1="170" x2="640" y2="220" stroke="#d1fae5" stroke-width="1.5" stroke-dasharray="6,4"/>
            <line x1="350" y1="170" x2="300" y2="18"  stroke="#d1fae5" stroke-width="1.5" stroke-dasharray="6,4"/>
            <line x1="350" y1="170" x2="290" y2="300" stroke="#d1fae5" stroke-width="1.5" stroke-dasharray="6,4"/>
            <!-- dots on lines -->
            <circle cx="210" cy="112" r="4" fill="#bbf7d0"/>
            <circle cx="200" cy="195" r="4" fill="#bbf7d0"/>
            <circle cx="485" cy="110" r="4" fill="#bbf7d0"/>
            <circle cx="495" cy="195" r="4" fill="#bbf7d0"/>
            <circle cx="325" cy="94"  r="4" fill="#bbf7d0"/>
            <circle cx="320" cy="235" r="4" fill="#bbf7d0"/>
        </svg>

        <!-- Central checkmark icon -->
        <div class="float-item center">✅</div>

        <!-- Feature icons -->
        <div class="float-item fi-1">🍎</div>
        <div class="float-item fi-2">🥛</div>
        <div class="float-item fi-3">🔔</div>
        <div class="float-item fi-4">🤝</div>
        <div class="float-item fi-5">🌾</div>
        <div class="float-item fi-6">🍞</div>

        <!-- Avatar circles -->
        <div class="avatar-float av-1">👨🏿‍🌾</div>
        <div class="avatar-float av-2">👩🏾‍🍳</div>
    </div>
</section>

<!-- ── STATS ── -->
<div class="stats-bar">
    @php
        $nbAnnonces   = \App\Models\Annonce::where('statut','disponible')->count();
        $nbFournisseurs = \App\Models\User::where('role','fournisseur')->count();
        $nbEchanges   = \App\Models\Reservation::where('statut','complétée')->count();
        $nbCategories = \App\Models\Categorie::count();
    @endphp
    <div class="stat-item">
        <div class="num">{{ $nbAnnonces > 0 ? $nbAnnonces : '0' }}</div>
        <div class="lbl">Annonces actives</div>
    </div>
    <div class="stat-item">
        <div class="num">{{ $nbFournisseurs > 0 ? $nbFournisseurs : '0' }}</div>
        <div class="lbl">Fournisseurs</div>
    </div>
    <div class="stat-item">
        <div class="num">{{ $nbEchanges > 0 ? $nbEchanges : '0' }}</div>
        <div class="lbl">Échanges réalisés</div>
    </div>
    <div class="stat-item">
        <div class="num">{{ $nbCategories }}</div>
        <div class="lbl">Catégories</div>
    </div>
</div>

<!-- ── HOW IT WORKS ── -->
<div style="max-width:1100px;margin:0 auto;">
<section class="section">
    <div class="section-label">Fonctionnement</div>
    <h2 class="section-title">Simple, rapide, efficace.</h2>
    <div class="steps-grid">
        @foreach([
            ['emoji'=>'👤','bg'=>'#dbeafe','title'=>'1. S\'inscrire','desc'=>'Créez votre compte gratuit en tant que fournisseur (restaurant, marché, artisan) ou acheteur (particulier, association, éleveur).'],
            ['emoji'=>'📢','bg'=>'#dcfce7','title'=>'2. Publier','desc'=>'Les fournisseurs publient leurs surplus en temps réel : photo, quantité, prix, localisation. Visible immédiatement.'],
            ['emoji'=>'🔍','bg'=>'#fef9c3','title'=>'3. Trouver','desc'=>'Les acheteurs cherchent par catégorie, localisation ou type d\'offre. Trouvez facilement les surplus près de chez vous.'],
            ['emoji'=>'🤝','bg'=>'#f3e8ff','title'=>'4. Échanger','desc'=>'Envoyez une demande de réservation. Le fournisseur accepte et vous organisez la collecte via la messagerie intégrée.'],
        ] as $s)
        <div class="step-card">
            <div class="step-num" style="background:{{ $s['bg'] }}">{{ $s['emoji'] }}</div>
            <h4>{{ $s['title'] }}</h4>
            <p>{{ $s['desc'] }}</p>
        </div>
        @endforeach
    </div>
</section>
</div>

<!-- ── BOUTIQUE / SHOP ── -->
@php $shopAnnonces = \App\Models\Annonce::with(['categorie','photoPrincipale','user'])->where('statut','disponible')->latest()->limit(8)->get(); @endphp
<section class="shop-section">
    <div class="shop-inner">
        <div class="section-label">Boutique · Shop</div>
        <div style="display:flex;justify-content:space-between;align-items:flex-end;flex-wrap:wrap;gap:16px;margin-bottom:8px;">
            <h2 class="section-title" style="margin:0;">Surplus disponibles maintenant</h2>
            <a href="{{ route('annonces.index') }}" style="font-size:.875rem;color:#16a34a;font-weight:600;text-decoration:none;background:#f0fdf4;border:1px solid #bbf7d0;padding:9px 20px;border-radius:50px;">
                Voir tout →
            </a>
        </div>
        <div class="shop-grid">
            @forelse($shopAnnonces as $annonce)
            @php
                $typeCss = ['vente'=>'type-vente','don'=>'type-don','alimentation_animale'=>'type-animal','transformation'=>'type-transform'];
                $typeLabel = ['vente'=>'Vente','don'=>'Don gratuit','alimentation_animale'=>'Animal','transformation'=>'Transformation'];
            @endphp
            <div class="shop-card">
                <!-- Image -->
                <div class="shop-img-wrap">
                    @if($annonce->photoPrincipale)
                        <img src="{{ asset('storage/'.$annonce->photoPrincipale->url) }}"
                             alt="{{ $annonce->titre }}"
                             onerror="this.parentElement.innerHTML='<div class=\'shop-img-emoji\'>{{ $annonce->categorie->icone ?? '🌿' }}</div>'">
                    @else
                        <div class="shop-img-emoji">{{ $annonce->categorie->icone ?? '🌿' }}</div>
                    @endif
                    <span class="shop-type-badge {{ $typeCss[$annonce->type_offre] ?? 'type-don' }}">
                        {{ $typeLabel[$annonce->type_offre] ?? $annonce->type_offre }}
                    </span>
                </div>
                <!-- Body -->
                <div class="shop-body">
                    <div class="shop-title">{{ Str::limit($annonce->titre, 45) }}</div>
                    <!-- Vendeur / Entreprise -->
                    <div class="shop-seller">
                        <div class="shop-seller-avatar">{{ strtoupper(substr($annonce->user->prenom ?? 'V', 0, 1)) }}</div>
                        <div>
                            @if($annonce->user->nom_structure)
                                <div class="shop-seller-company">🏪 {{ $annonce->user->nom_structure }}</div>
                            @endif
                            <div class="shop-seller-name">{{ $annonce->user->prenom ?? '—' }} {{ $annonce->user->nom ?? '' }}</div>
                        </div>
                    </div>
                    <!-- Meta -->
                    <div class="shop-meta">
                        <span><i class="fas fa-map-marker-alt"></i>{{ Str::limit($annonce->adresse_collecte ?? 'Abidjan', 18) }}</span>
                        <span><i class="fas fa-weight"></i>{{ $annonce->quantite }} {{ $annonce->unite }}</span>
                        @if($annonce->categorie)<span><i class="fas fa-tag"></i>{{ $annonce->categorie->nom }}</span>@endif
                    </div>
                    <!-- Footer -->
                    <div class="shop-footer">
                        @if($annonce->type_offre === 'don')
                            <div class="shop-price free">Gratuit</div>
                        @elseif($annonce->prix > 0)
                            <div class="shop-price">{{ number_format($annonce->prix, 0, ',', ' ') }} <span class="fcfa">FCFA</span></div>
                        @else
                            <div class="shop-price" style="color:#888;font-size:.9rem;">À discuter</div>
                        @endif
                        <a href="{{ route('annonces.show', $annonce) }}" class="shop-btn">Voir →</a>
                    </div>
                </div>
            </div>
            @empty
            {{-- Exemples si aucune annonce en DB --}}
            @foreach([
                ['emoji'=>'🍎','title'=>'Cageots de mangues fraîches','vendeur'=>'Marché Adjamé','prenom'=>'Kouamé','type'=>'type-vente','label'=>'Vente','price'=>'3 500','lieu'=>'Adjamé','q'=>'15 kg','cat'=>'Fruits & Légumes'],
                ['emoji'=>'🍞','title'=>'Pain invendu du jour','vendeur'=>'Boulangerie du Plateau','prenom'=>'Aya','type'=>'type-don','label'=>'Don gratuit','price'=>null,'lieu'=>'Plateau','q'=>'8 unités','cat'=>'Pain & Pâtisserie'],
                ['emoji'=>'🥛','title'=>'Lait frais excédentaire','vendeur'=>'Ferme Tiassalé','prenom'=>'Moussa','type'=>'type-vente','label'=>'Vente','price'=>'1 200','lieu'=>'Yopougon','q'=>'20 L','cat'=>'Produits Laitiers'],
                ['emoji'=>'🌾','title'=>'Sacs de céréales invendues','vendeur'=>'Coopérative Abobo','prenom'=>'Fatou','type'=>'type-animal','label'=>'Animal','price'=>'800','lieu'=>'Abobo','q'=>'50 kg','cat'=>'Céréales'],
            ] as $ex)
            <div class="shop-card">
                <div class="shop-img-wrap">
                    <div class="shop-img-emoji">{{ $ex['emoji'] }}</div>
                    <span class="shop-type-badge {{ $ex['type'] }}">{{ $ex['label'] }}</span>
                </div>
                <div class="shop-body">
                    <div class="shop-title">{{ $ex['title'] }}</div>
                    <div class="shop-seller">
                        <div class="shop-seller-avatar">{{ strtoupper(substr($ex['prenom'],0,1)) }}</div>
                        <div>
                            <div class="shop-seller-company">🏪 {{ $ex['vendeur'] }}</div>
                            <div class="shop-seller-name">{{ $ex['prenom'] }}</div>
                        </div>
                    </div>
                    <div class="shop-meta">
                        <span><i class="fas fa-map-marker-alt"></i>{{ $ex['lieu'] }}</span>
                        <span><i class="fas fa-weight"></i>{{ $ex['q'] }}</span>
                        <span><i class="fas fa-tag"></i>{{ $ex['cat'] }}</span>
                    </div>
                    <div class="shop-footer">
                        @if($ex['price'])
                            <div class="shop-price">{{ $ex['price'] }} <span class="fcfa">FCFA</span></div>
                        @else
                            <div class="shop-price free">Gratuit</div>
                        @endif
                        <a href="{{ route('connexion') }}" class="shop-btn" onclick="openAuth('login');return false;">Voir →</a>
                    </div>
                </div>
            </div>
            @endforeach
            @endforelse
        </div>
    </div>
</section>

<!-- ── CATÉGORIES ── -->
<div style="max-width:1100px;margin:0 auto;">
<section class="categories-section">
    <div class="section-label">Explorer par catégorie</div>
    <h2 class="section-title">Tous types de surplus alimentaires</h2>
    <div class="cats-grid">
        @foreach(\App\Models\Categorie::withCount(['annonces' => fn($q) => $q->where('statut','disponible')])->get() as $cat)
        <a href="{{ route('annonces.index', ['categorie' => $cat->id]) }}" class="cat-pill">
            <span>{{ $cat->icone }}</span>
            <span>{{ $cat->nom }}</span>
            <span class="cat-count">({{ $cat->annonces_count }})</span>
        </a>
        @endforeach
    </div>
</section>
</div>

<!-- ── CTA BAND ── -->
<section class="cta-band">
    <h2>Rejoignez le mouvement<br>anti-gaspillage</h2>
    <p>Des centaines de fournisseurs et acheteurs en Côte d'Ivoire échangent leurs surplus chaque jour. Rejoignez-les gratuitement.</p>
    <a href="{{ route('inscription') }}" class="btn-cta-white">
        Créer mon compte gratuit →
    </a>
</section>

<!-- ── FOOTER ── -->
<footer>
    <div class="logo">🌿 AntiGaspiCI</div>
    <nav>
        <a href="{{ route('annonces.index') }}">Annonces</a>
        <a href="{{ route('blog') }}">Blog</a>
        <a href="{{ route('comment-ca-marche') }}">Fonctionnement</a>
        <a href="{{ route('contact') }}">Contact</a>
        <a href="{{ route('connexion') }}">Connexion</a>
    </nav>
    <div class="copy">© {{ date('Y') }} AntiGaspiCI — Côte d'Ivoire</div>
</footer>

<!-- ── AUTH MODAL ── -->
<div class="auth-overlay" id="authOverlay" onclick="closeAuthIfBg(event)">
    <div class="auth-modal" id="authModal">

        <!-- Close -->
        <button class="auth-close" onclick="closeAuth()">✕</button>

        <!-- LEFT PANEL -->
        <div class="auth-left">

            <!-- LOGIN FORM -->
            <div id="loginForm">
                <h2>Bon retour !</h2>
                <p class="sub">Simplifiez vos échanges et réduisez le gaspillage<br>avec <strong>AntiGaspiCI</strong>. Commencez gratuitement.</p>

                @if(session('error'))
                    <div class="auth-error show">{{ session('error') }}</div>
                @endif
                <div class="auth-error" id="loginError"></div>

                <form action="{{ route('connecter') }}" method="POST">
                    @csrf
                    <div class="auth-input-wrap">
                        <input type="email" name="email" placeholder="Adresse e-mail" required value="{{ old('email') }}" autocomplete="email">
                    </div>
                    <div class="auth-input-wrap">
                        <input type="password" name="password" id="pwdInput" placeholder="Mot de passe" required autocomplete="current-password">
                        <span class="eye" onclick="togglePwd()"><i class="fas fa-eye" id="eyeIcon"></i></span>
                    </div>
                    <a href="{{ route('password.email.form') }}" class="auth-forgot">Mot de passe oublié ?</a>
                    <button type="submit" class="btn-auth-submit">Connexion</button>
                </form>

                <div class="auth-divider"><span>ou continuer avec</span></div>
                <div class="social-btns">
                    <button class="btn-social google"><i class="fab fa-google"></i></button>
                    <button class="btn-social apple"><i class="fab fa-apple"></i></button>
                    <button class="btn-social fb"><i class="fab fa-facebook-f"></i></button>
                </div>

                <p class="auth-switch">Pas encore membre ? <a href="#" onclick="switchToRegister()">S'inscrire</a></p>
            </div>

            <!-- REGISTER FORM -->
            <div id="registerForm" style="display:none;">
                <h2>Créer un compte</h2>
                <p class="sub">Rejoignez des centaines de fournisseurs et acheteurs<br>sur <strong>AntiGaspiCI</strong>.</p>

                @if($errors->hasAny(['nom','prenom','email','password','role','telephone']))
                    <div class="auth-error show" id="registerError">
                        @foreach($errors->all() as $err)<div>• {{ $err }}</div>@endforeach
                    </div>
                @else
                    <div class="auth-error" id="registerError"></div>
                @endif

                <form action="{{ route('inscrire') }}" method="POST">
                    @csrf
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                        <div class="auth-input-wrap">
                            <input type="text" name="prenom" placeholder="Prénom" required value="{{ old('prenom') }}">
                        </div>
                        <div class="auth-input-wrap">
                            <input type="text" name="nom" placeholder="Nom" required value="{{ old('nom') }}">
                        </div>
                    </div>
                    <div class="auth-input-wrap">
                        <input type="email" name="email" placeholder="Adresse e-mail" required value="{{ old('email') }}"
                            style="{{ $errors->has('email') ? 'border-color:#dc2626' : '' }}">
                    </div>
                    <div class="auth-input-wrap">
                        <input type="password" name="password" placeholder="Mot de passe (min. 8 caractères)" required
                            style="{{ $errors->has('password') ? 'border-color:#dc2626' : '' }}">
                    </div>
                    <div class="auth-input-wrap">
                        <input type="password" name="password_confirmation" placeholder="Confirmer le mot de passe" required>
                    </div>
                    <div style="display:flex;gap:16px;margin-bottom:14px;">
                        <label style="display:flex;align-items:center;gap:6px;cursor:pointer;font-size:.85rem;font-weight:600;">
                            <input type="radio" name="role" value="acheteur" {{ old('role','acheteur')==='acheteur'?'checked':'' }}> Acheteur
                        </label>
                        <label style="display:flex;align-items:center;gap:6px;cursor:pointer;font-size:.85rem;font-weight:600;">
                            <input type="radio" name="role" value="fournisseur" {{ old('role')==='fournisseur'?'checked':'' }}> Fournisseur
                        </label>
                    </div>
                    <input type="hidden" name="telephone" value="">
                    <button type="submit" class="btn-auth-submit">Créer mon compte</button>
                </form>

                <p class="auth-switch" style="margin-top:16px;">Déjà membre ? <a href="#" onclick="switchToLogin()">Se connecter</a></p>
            </div>
        </div>

        <!-- RIGHT PANEL -->
        <div class="auth-right">
            <div class="auth-illus">🌿</div>
            <div class="auth-right-title">Réduis le gaspillage,<br><strong>maximise l'impact</strong></div>
            <p class="auth-right-sub">Des surplus alimentaires transformés en opportunités chaque jour en Côte d'Ivoire.</p>

            <div class="mini-card">
                <div class="mini-card-icon">🍎</div>
                <div>
                    <div class="mini-card-label">Fruits & Légumes</div>
                    <div class="mini-card-sub">12 annonces disponibles</div>
                    <div class="mini-card-prog"><div class="mini-card-fill"></div></div>
                </div>
            </div>

            <div class="auth-avatars">
                <div class="auth-av">👨🏿‍🌾</div>
                <div class="auth-av">👩🏾‍🍳</div>
                <div class="auth-av">🧑🏽‍💼</div>
                <div class="auth-av">👩🏿‍🤝‍👩🏾</div>
            </div>
        </div>

    </div>
</div>

<script>
function openAuth(mode) {
    document.getElementById('authOverlay').classList.add('open');
    document.body.style.overflow = 'hidden';
    if (mode === 'register') { switchToRegister(); }
    else { switchToLogin(); }
}
function closeAuth() {
    document.getElementById('authOverlay').classList.remove('open');
    document.body.style.overflow = '';
}
function closeAuthIfBg(e) {
    if (e.target === document.getElementById('authOverlay')) closeAuth();
}
function switchToRegister() {
    document.getElementById('loginForm').style.display = 'none';
    document.getElementById('registerForm').style.display = 'block';
}
function switchToLogin() {
    document.getElementById('registerForm').style.display = 'none';
    document.getElementById('loginForm').style.display = 'block';
}
function togglePwd() {
    const p = document.getElementById('pwdInput');
    const i = document.getElementById('eyeIcon');
    if (p.type === 'password') { p.type = 'text'; i.className = 'fas fa-eye-slash'; }
    else { p.type = 'password'; i.className = 'fas fa-eye'; }
}
// Ouvrir automatiquement si erreur de validation
@if($errors->hasAny(['nom','prenom','role','telephone']) || ($errors->hasAny(['email','password']) && old('prenom')))
    document.addEventListener('DOMContentLoaded', () => openAuth('register'));
@elseif($errors->any() || session('error'))
    document.addEventListener('DOMContentLoaded', () => openAuth('login'));
@endif
// Escape key closes modal
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeAuth(); });
</script>

<!-- ── CHAT IA WIDGET ────────────────────────────────────────── -->
<style>
#chat-fab {
    position: fixed; bottom: 28px; right: 28px; z-index: 9000;
    width: 56px; height: 56px; border-radius: 50%;
    background: linear-gradient(135deg, #16a34a, #15803d);
    color: #fff; border: none; cursor: pointer;
    box-shadow: 0 4px 20px rgba(22,163,74,.4);
    font-size: 1.5rem; display: flex; align-items: center; justify-content: center;
    transition: transform .2s, box-shadow .2s;
}
#chat-fab:hover { transform: scale(1.1); box-shadow: 0 6px 28px rgba(22,163,74,.5); }

#chat-window {
    position: fixed; bottom: 96px; right: 28px; z-index: 9000;
    width: 360px; max-height: 520px;
    background: #fff; border-radius: 20px;
    box-shadow: 0 8px 40px rgba(0,0,0,.18);
    display: none; flex-direction: column; overflow: hidden;
    border: 1px solid #e5e7eb;
}
#chat-window.open { display: flex; animation: slideUp .25s ease; }
@keyframes slideUp { from { opacity:0; transform:translateY(16px) } to { opacity:1; transform:translateY(0) } }

#chat-header {
    background: linear-gradient(135deg, #16a34a, #15803d);
    color: #fff; padding: 14px 18px;
    display: flex; align-items: center; justify-content: space-between;
}
#chat-header .title { font-weight: 700; font-size: .95rem; }
#chat-header .subtitle { font-size: .75rem; opacity: .85; }
#chat-close { background: none; border: none; color: #fff; font-size: 1.2rem; cursor: pointer; padding: 0 4px; }

#chat-messages {
    flex: 1; overflow-y: auto; padding: 16px;
    display: flex; flex-direction: column; gap: 10px;
    min-height: 200px;
}
.chat-msg { max-width: 85%; padding: 10px 14px; border-radius: 16px; font-size: .875rem; line-height: 1.5; }
.chat-msg.user { background: #16a34a; color: #fff; align-self: flex-end; border-bottom-right-radius: 4px; }
.chat-msg.bot  { background: #f3f4f6; color: #111; align-self: flex-start; border-bottom-left-radius: 4px; }
.chat-msg.bot.typing { color: #9ca3af; font-style: italic; }

#chat-form {
    display: flex; gap: 8px; padding: 12px 14px;
    border-top: 1px solid #f0f0f0;
}
#chat-input {
    flex: 1; border: 1.5px solid #e5e7eb; border-radius: 12px;
    padding: 9px 14px; font-size: .875rem; outline: none;
    font-family: 'Inter', sans-serif; resize: none;
    transition: border-color .2s;
}
#chat-input:focus { border-color: #16a34a; }
#chat-send {
    background: #16a34a; color: #fff; border: none;
    border-radius: 12px; padding: 9px 16px; cursor: pointer;
    font-size: .875rem; font-weight: 600; transition: background .2s;
}
#chat-send:hover { background: #15803d; }
#chat-send:disabled { background: #9ca3af; cursor: not-allowed; }
</style>

<!-- Bouton flottant -->
<button id="chat-fab" onclick="toggleChat()" title="Assistant IA AntiGaspiCI">
    💬
</button>

<!-- Fenêtre de chat -->
<div id="chat-window">
    <div id="chat-header">
        <div>
            <div class="title">🌿 Assistant AntiGaspiCI</div>
            <div class="subtitle">Posez-moi toutes vos questions</div>
        </div>
        <button id="chat-close" onclick="toggleChat()">✕</button>
    </div>
    <div id="chat-messages">
        <div class="chat-msg bot">Bonjour ! Je suis l'assistant d'AntiGaspiCI 🌿<br>Comment puis-je vous aider ?</div>
    </div>
    <form id="chat-form" onsubmit="sendMessage(event)">
        <textarea id="chat-input" placeholder="Écrivez votre message..." rows="1"
            onkeydown="if(event.key==='Enter'&&!event.shiftKey){event.preventDefault();sendMessage(event);}"></textarea>
        <button id="chat-send" type="submit">Envoyer</button>
    </form>
</div>

<script>
(function() {
    const history = [];
    let isOpen = false;

    window.toggleChat = function() {
        isOpen = !isOpen;
        const win = document.getElementById('chat-window');
        const fab = document.getElementById('chat-fab');
        win.classList.toggle('open', isOpen);
        fab.textContent = isOpen ? '✕' : '💬';
        if (isOpen) document.getElementById('chat-input').focus();
    };

    window.sendMessage = async function(e) {
        e.preventDefault();
        const input = document.getElementById('chat-input');
        const text  = input.value.trim();
        if (!text) return;

        appendMessage('user', text);
        history.push({ role: 'user', content: text });
        input.value = '';
        input.style.height = 'auto';

        const send = document.getElementById('chat-send');
        send.disabled = true;

        const botEl = appendMessage('bot', '', true);

        try {
            const res = await fetch('{{ route("chat") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]') ?.content
                        ?? '{{ csrf_token() }}'
                },
                body: JSON.stringify({ message: text, history: history.slice(0, -1) })
            });

            if (!res.ok) throw new Error('Erreur serveur');

            const reader = res.body.getReader();
            const decoder = new TextDecoder();
            let fullText = '';
            let buffer = '';

            while (true) {
                const { done, value } = await reader.read();
                if (done) break;

                buffer += decoder.decode(value, { stream: true });
                const lines = buffer.split('\n');
                buffer = lines.pop();

                for (const line of lines) {
                    if (!line.startsWith('data: ')) continue;
                    const raw = line.slice(6).trim();
                    if (raw === '[DONE]') break;
                    try {
                        const parsed = JSON.parse(raw);
                        if (parsed.text) {
                            fullText += parsed.text;
                            botEl.textContent = fullText;
                            botEl.classList.remove('typing');
                            scrollChat();
                        }
                    } catch {}
                }
            }

            if (fullText) history.push({ role: 'assistant', content: fullText });

        } catch (err) {
            botEl.textContent = "Désolé, une erreur s'est produite. Réessayez.";
            botEl.style.color = '#ef4444';
        } finally {
            send.disabled = false;
            input.focus();
        }
    };

    function appendMessage(role, text, isTyping = false) {
        const msgs = document.getElementById('chat-messages');
        const el = document.createElement('div');
        el.className = 'chat-msg ' + role + (isTyping ? ' typing' : '');
        el.textContent = isTyping ? '…' : text;
        msgs.appendChild(el);
        scrollChat();
        return el;
    }

    function scrollChat() {
        const msgs = document.getElementById('chat-messages');
        msgs.scrollTop = msgs.scrollHeight;
    }

    // Auto-resize textarea
    document.getElementById('chat-input').addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = Math.min(this.scrollHeight, 120) + 'px';
    });
})();
</script>

</body>
</html>
