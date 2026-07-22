<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>AntiGaspiCI — Plateforme Anti-Gaspillage Alimentaire en Côte d'Ivoire</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="AntiGaspiCI connecte fournisseurs et acheteurs pour transformer les surplus alimentaires en opportunités en Côte d'Ivoire.">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Rubik:wght@400;500;600;700;800;900&family=Nunito+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css"/>
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <style>
        :root {
            --green:#16a34a; --green-dark:#15803d; --green-light:#22c55e;
            --green-50:#f0fdf4; --green-100:#dcfce7;
            --orange:#f97316; --orange-dark:#ea580c;
            --dark:#0a0a0a; --text:#0f172a; --muted:#64748b; --muted-2:#94a3b8;
            --border:#e2e8f0; --surface:#f8fafc; --white:#ffffff;
        }
        *{margin:0;padding:0;box-sizing:border-box;}
        html{scroll-behavior:smooth;}
        body{font-family:'Nunito Sans',sans-serif;background:#fff;color:var(--text);overflow-x:hidden;}
        h1,h2,h3,h4{font-family:'Rubik',sans-serif;}
        ::-webkit-scrollbar{width:5px;}
        ::-webkit-scrollbar-thumb{background:var(--green);border-radius:3px;}

        /* ── NAVBAR ── */
        .navbar-custom{
            position:fixed;top:0;left:0;right:0;
            z-index:200;
            display:flex;align-items:center;
            padding:0 32px;height:64px;
            background:rgba(255,255,255,.97);backdrop-filter:blur(20px);
            border-top:3px solid var(--green);
            border-bottom:1px solid rgba(0,0,0,.07);
            box-shadow:0 2px 16px rgba(0,0,0,.06);
            transition:box-shadow .3s,background .3s;
        }
        .navbar-custom.scrolled{box-shadow:0 4px 24px rgba(0,0,0,.1);background:rgba(255,255,255,1);}
        .nav-inner{max-width:1200px;width:100%;margin:0 auto;display:flex;align-items:center;justify-content:space-between;}
        /* Logo */
        .navbar-logo{font-family:'Rubik',sans-serif;font-weight:900;font-size:1.12rem;
            color:var(--text);text-decoration:none;display:flex;align-items:center;gap:10px;letter-spacing:-.3px;}
        .logo-icon{width:36px;height:36px;border-radius:10px;
            background:linear-gradient(135deg,#16a34a 0%,#22c55e 100%);
            display:flex;align-items:center;justify-content:center;
            box-shadow:0 2px 8px rgba(22,163,74,.35);flex-shrink:0;}
        .navbar-logo .logo-text .anti{color:var(--text);}
        .navbar-logo .logo-text .gaspi{color:var(--green);}
        .navbar-logo .logo-text .ci{color:var(--text);opacity:.5;font-size:.95rem;}
        /* Links */
        .navbar-links{display:flex;gap:0;list-style:none;align-items:center;}
        .navbar-links li{position:relative;}
        .navbar-links a{text-decoration:none;color:var(--muted);font-size:.86rem;font-weight:600;
            padding:8px 14px;border-radius:8px;transition:all .2s;display:flex;align-items:center;gap:5px;}
        .navbar-links a:hover{color:var(--text);background:var(--green-50);}
        /* Actions */
        .navbar-actions{display:flex;gap:8px;align-items:center;}
        .btn-ghost{background:none;border:none;color:var(--muted);font-weight:600;
            font-size:.86rem;padding:8px 16px;border-radius:8px;cursor:pointer;
            transition:all .2s;font-family:'Nunito Sans',sans-serif;}
        .btn-ghost:hover{background:var(--surface);color:var(--text);}
        .btn-dark{background:var(--green);color:#fff;border:none;padding:9px 20px;
            border-radius:8px;font-size:.86rem;font-weight:700;cursor:pointer;
            transition:all .2s;font-family:'Nunito Sans',sans-serif;text-decoration:none;
            display:inline-flex;align-items:center;gap:7px;
            box-shadow:0 2px 8px rgba(22,163,74,.3);}
        .btn-dark:hover{background:#15803d;transform:translateY(-1px);color:#fff;box-shadow:0 4px 14px rgba(22,163,74,.4);}

        /* ── HERO ── */
        .hero{
            background:#FFB300 url('/img/lion-bg.png') center center / cover no-repeat;
            min-height:100vh;display:flex;align-items:center;
            position:relative;overflow:hidden;padding:110px 48px 100px;
        }
        /* couche sombre sur la gauche (texte lisible) + légère sur la droite (lion visible) */
        .hero::after{
            content:'';position:absolute;inset:0;pointer-events:none;
            background:linear-gradient(100deg,
                rgba(10,10,10,.88) 0%,
                rgba(10,10,10,.75) 40%,
                rgba(10,10,10,.30) 70%,
                rgba(0,0,0,.10) 100%);
        }
        .hero-grid{
            position:absolute;inset:0;z-index:1;
            background-image:linear-gradient(rgba(255,255,255,.025) 1px,transparent 1px),
                linear-gradient(90deg,rgba(255,255,255,.025) 1px,transparent 1px);
            background-size:56px 56px;pointer-events:none;
        }
        .hero-glow-1{position:absolute;top:-150px;right:-80px;width:600px;height:600px;
            border-radius:50%;background:radial-gradient(circle,rgba(255,179,0,.18) 0%,transparent 70%);
            pointer-events:none;z-index:1;}
        .hero-glow-2{position:absolute;bottom:-100px;left:-100px;width:450px;height:450px;
            border-radius:50%;background:radial-gradient(circle,rgba(255,179,0,.08) 0%,transparent 70%);
            pointer-events:none;z-index:1;}

        .hero-inner{max-width:1180px;margin:0 auto;width:100%;
            display:grid;grid-template-columns:1fr 1fr;gap:64px;align-items:center;
            position:relative;z-index:3;}

        .hero-badge{display:inline-flex;align-items:center;gap:8px;
            background:rgba(255,179,0,.18);border:1px solid rgba(255,179,0,.45);
            color:#FFD54F;font-size:.75rem;font-weight:700;padding:7px 16px;
            border-radius:50px;margin-bottom:26px;letter-spacing:.6px;text-transform:uppercase;}
        .badge-dot{width:6px;height:6px;border-radius:50%;background:#FFD54F;
            animation:blink 2s infinite;}
        @keyframes blink{0%,100%{opacity:1;transform:scale(1);}50%{opacity:.5;transform:scale(.8);}}

        .hero-title{font-size:clamp(2.6rem,4.5vw,4rem);font-weight:900;line-height:1.06;
            letter-spacing:-2px;color:#fff;margin-bottom:22px;}
        .hero-title .hl{background:linear-gradient(135deg,#FFD54F,#FFB300);
            -webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;}
        .hero-title .hl-o{background:linear-gradient(135deg,#FFECB3,#FFD54F);
            -webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;}

        .hero-sub{font-size:1.02rem;color:rgba(255,255,255,.68);line-height:1.72;
            max-width:450px;margin-bottom:34px;}

        .hero-cta{display:flex;gap:12px;flex-wrap:wrap;margin-bottom:44px;}
        .btn-orange{background:#FFB300;color:#0a0a0a;padding:14px 30px;border-radius:50px;
            font-family:'Rubik',sans-serif;font-weight:800;font-size:.95rem;text-decoration:none;
            display:inline-flex;align-items:center;gap:8px;transition:all .25s;
            box-shadow:0 4px 20px rgba(255,179,0,.45);}
        .btn-orange:hover{background:#FFA000;transform:translateY(-2px);
            box-shadow:0 8px 30px rgba(255,179,0,.55);color:#0a0a0a;}
        .btn-glass{background:rgba(255,179,0,.1);color:#FFD54F;padding:14px 30px;
            border-radius:50px;font-weight:700;font-size:.95rem;text-decoration:none;
            border:1px solid rgba(255,179,0,.35);display:inline-flex;align-items:center;gap:8px;
            transition:all .25s;backdrop-filter:blur(8px);}
        .btn-glass:hover{background:rgba(255,179,0,.2);border-color:rgba(255,179,0,.6);
            transform:translateY(-2px);color:#FFD54F;}

        .hero-proof{display:flex;align-items:center;gap:14px;}
        .proof-avs{display:flex;}
        .proof-av{width:38px;height:38px;border-radius:50%;border:2.5px solid rgba(255,179,0,.5);
            display:flex;align-items:center;justify-content:center;font-size:1.1rem;
            margin-left:-9px;}
        .proof-av:first-child{margin-left:0;}
        .proof-txt{font-size:.8rem;color:rgba(255,255,255,.72);line-height:1.5;}
        .proof-txt strong{color:#FFD54F;}

        /* Hero phone mock */
        .hero-right{position:relative;display:flex;align-items:center;justify-content:center;}
        .phone{width:274px;background:rgba(255,255,255,.04);border:1.5px solid rgba(255,255,255,.11);
            border-radius:38px;padding:18px 14px;backdrop-filter:blur(20px);
            box-shadow:0 40px 80px rgba(0,0,0,.4),inset 0 1px 0 rgba(255,255,255,.08);}
        .phone-notch{width:76px;height:20px;background:rgba(0,0,0,.45);
            border-radius:50px;margin:0 auto 14px;}
        .pcard{background:rgba(255,255,255,.07);border:1px solid rgba(255,255,255,.1);
            border-radius:14px;padding:12px;margin-bottom:9px;transition:all .2s;cursor:pointer;}
        .pcard:hover{background:rgba(255,255,255,.12);}
        .pcard-top{display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:7px;}
        .pcard-ico{width:40px;height:40px;border-radius:10px;
            display:flex;align-items:center;justify-content:center;font-size:1.3rem;}
        .pbadge{font-size:.6rem;font-weight:700;padding:3px 8px;border-radius:50px;letter-spacing:.5px;}
        .pbadge.don{background:rgba(34,197,94,.2);color:#4ade80;border:1px solid rgba(34,197,94,.3);}
        .pbadge.vente{background:rgba(59,130,246,.2);color:#93c5fd;border:1px solid rgba(59,130,246,.3);}
        .pbadge.urgent{background:rgba(249,115,22,.2);color:#fb923c;border:1px solid rgba(249,115,22,.3);}
        .pcard-title{font-size:.78rem;font-weight:700;color:#fff;margin-bottom:2px;}
        .pcard-sub{font-size:.65rem;color:rgba(255,255,255,.45);}
        .pcard-foot{display:flex;align-items:center;justify-content:space-between;margin-top:8px;}
        .pprice{font-size:.82rem;font-weight:800;}
        .pprice.free{color:#4ade80;} .pprice.paid{color:#fff;}
        .ploc{font-size:.62rem;color:rgba(255,255,255,.38);display:flex;align-items:center;gap:3px;}

        .notif-float{position:absolute;top:-16px;right:-32px;
            background:#fff;border-radius:14px;padding:11px 14px;
            box-shadow:0 8px 28px rgba(0,0,0,.2);display:flex;align-items:center;gap:10px;
            width:190px;animation:float 3s ease-in-out infinite;}
        .nf-ico{width:34px;height:34px;border-radius:9px;background:var(--green-50);
            display:flex;align-items:center;justify-content:center;font-size:1rem;flex-shrink:0;}
        .nf-title{font-size:.72rem;font-weight:700;color:var(--text);}
        .nf-sub{font-size:.62rem;color:var(--muted);}

        .impact-pill{position:absolute;bottom:24px;left:-36px;
            background:rgba(15,15,15,.92);border:1px solid rgba(255,179,0,.4);
            border-radius:50px;padding:9px 14px;display:flex;align-items:center;gap:8px;
            animation:float 4s ease-in-out infinite 1.2s;}
        .ip-ico{font-size:1.1rem;}
        .ip-txt{font-size:.68rem;color:rgba(255,255,255,.65);}
        .ip-txt strong{display:block;color:#FFD54F;font-size:.82rem;font-family:'Rubik',sans-serif;}

        @keyframes float{0%,100%{transform:translateY(0);}50%{transform:translateY(-8px);}}

        /* ── TICKER ── */
        .ticker{background:var(--green-50);border-top:1px solid var(--green-100);
            border-bottom:1px solid var(--green-100);padding:11px 0;overflow:hidden;}
        .ticker-track{display:flex;gap:36px;animation:tick 22s linear infinite;white-space:nowrap;}
        .t-item{display:flex;align-items:center;gap:7px;font-size:.8rem;
            font-weight:700;color:var(--green);flex-shrink:0;}
        .t-dot{width:5px;height:5px;border-radius:50%;background:var(--green);}
        @keyframes tick{from{transform:translateX(0);}to{transform:translateX(-50%);}}

        /* ── STATS ── */
        .stats-sec{background:var(--dark);padding:52px 48px;}
        .stats-grid{max-width:1100px;margin:0 auto;
            display:grid;grid-template-columns:repeat(4,1fr);gap:20px;}
        .stat-card{background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.08);
            border-radius:18px;padding:26px 20px;text-align:center;
            transition:all .25s;position:relative;overflow:hidden;}
        .stat-card:hover{background:rgba(255,255,255,.09);transform:translateY(-4px);}
        .stat-card::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;border-radius:3px 3px 0 0;}
        .stat-card:nth-child(1)::before{background:linear-gradient(90deg,#22c55e,#4ade80);}
        .stat-card:nth-child(2)::before{background:linear-gradient(90deg,#3b82f6,#60a5fa);}
        .stat-card:nth-child(3)::before{background:linear-gradient(90deg,#f97316,#fb923c);}
        .stat-card:nth-child(4)::before{background:linear-gradient(90deg,#a855f7,#c084fc);}
        .stat-ico{font-size:1.7rem;margin-bottom:10px;}
        .stat-num{font-family:'Rubik',sans-serif;font-size:2.3rem;font-weight:900;
            color:#fff;line-height:1;margin-bottom:5px;}
        .stat-lbl{font-size:.78rem;color:#6b7280;font-weight:500;}

        /* ── SECTION HEADER PATTERN ── */
        .sec-tag{display:inline-flex;align-items:center;gap:7px;font-size:.7rem;
            font-weight:700;letter-spacing:2px;color:var(--green);text-transform:uppercase;margin-bottom:10px;}
        .sec-line{width:22px;height:2px;background:var(--green);border-radius:2px;}
        .sec-title{font-family:'Rubik',sans-serif;font-size:clamp(1.75rem,3.5vw,2.7rem);
            font-weight:800;letter-spacing:-1px;line-height:1.14;color:var(--text);}
        .sec-title .hl{color:var(--green);}
        .sec-sub{font-size:.98rem;color:var(--muted);line-height:1.7;margin-top:11px;max-width:520px;}

        /* ── HOW IT WORKS ── */
        .how-sec{padding:96px 48px;background:var(--surface);}
        .how-inner{max-width:1100px;margin:0 auto;}
        .how-hdr{margin-bottom:60px;}
        .steps{display:grid;grid-template-columns:repeat(4,1fr);gap:0;position:relative;}
        .steps::before{content:'';position:absolute;top:30px;
            left:calc(12.5% + 22px);right:calc(12.5% + 22px);height:2px;
            background:linear-gradient(90deg,var(--green-100),var(--green),var(--green-100));}
        .step{display:flex;flex-direction:column;align-items:center;text-align:center;padding:0 20px;}
        .step-bub{width:60px;height:60px;border-radius:50%;display:flex;align-items:center;
            justify-content:center;margin-bottom:22px;position:relative;z-index:2;
            box-shadow:0 4px 14px rgba(0,0,0,.09);}
        .step-bub i{font-size:1.3rem;}
        .step-n{position:absolute;top:-5px;right:-5px;width:18px;height:18px;border-radius:50%;
            background:var(--green);color:#fff;font-size:.6rem;font-weight:800;
            display:flex;align-items:center;justify-content:center;font-family:'Rubik',sans-serif;}
        .step-t{font-size:.95rem;font-weight:700;margin-bottom:7px;color:var(--text);}
        .step-d{font-size:.82rem;color:var(--muted);line-height:1.65;}

        /* ── SHOP ── */
        .shop-sec{padding:96px 48px;background:#fff;}
        .shop-inner{max-width:1200px;margin:0 auto;}
        .shop-hdr{display:flex;justify-content:space-between;align-items:flex-end;
            flex-wrap:wrap;gap:18px;margin-bottom:38px;}
        .btn-all{display:inline-flex;align-items:center;gap:7px;font-size:.875rem;
            font-weight:700;color:var(--green);background:var(--green-50);
            border:1px solid var(--green-100);padding:10px 20px;border-radius:50px;
            text-decoration:none;transition:all .2s;}
        .btn-all:hover{background:var(--green);color:#fff;border-color:var(--green);}
        .shop-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(255px,1fr));gap:22px;}
        .shop-card{background:#fff;border-radius:20px;overflow:hidden;
            border:1.5px solid var(--border);cursor:pointer;
            transition:all .28s;display:flex;flex-direction:column;}
        .shop-card:hover{box-shadow:0 20px 56px rgba(0,0,0,.10);transform:translateY(-6px);border-color:transparent;}
        .s-img{position:relative;height:195px;overflow:hidden;background:#f3f4f6;}
        .s-img img{width:100%;height:100%;object-fit:cover;transition:transform .4s ease;}
        .shop-card:hover .s-img img{transform:scale(1.06);}
        .s-emoji{width:100%;height:100%;display:flex;align-items:center;justify-content:center;
            font-size:3.8rem;background:linear-gradient(135deg,#f0fdf4,#dcfce7);transition:transform .4s;}
        .shop-card:hover .s-emoji{transform:scale(1.07);}
        .s-type{position:absolute;top:11px;left:11px;font-size:.65rem;font-weight:700;
            letter-spacing:.7px;text-transform:uppercase;padding:4px 11px;border-radius:50px;
            backdrop-filter:blur(8px);}
        .type-don{background:rgba(22,163,74,.14);color:#15803d;border:1px solid rgba(22,163,74,.28);}
        .type-vente{background:rgba(29,78,216,.12);color:#1d4ed8;border:1px solid rgba(29,78,216,.24);}
        .type-animal{background:rgba(234,88,12,.12);color:#ea580c;border:1px solid rgba(234,88,12,.24);}
        .type-transform{background:rgba(147,51,234,.12);color:#9333ea;border:1px solid rgba(147,51,234,.24);}
        .s-urgent{position:absolute;top:11px;right:11px;background:rgba(239,68,68,.88);
            color:#fff;font-size:.6rem;font-weight:700;padding:3px 8px;border-radius:50px;
            display:flex;align-items:center;gap:3px;}
        .s-body{padding:16px;flex:1;display:flex;flex-direction:column;}
        .s-title{font-family:'Rubik',sans-serif;font-size:.92rem;font-weight:700;
            color:var(--text);margin-bottom:8px;line-height:1.3;}
        .s-seller{display:flex;align-items:center;gap:7px;margin-bottom:9px;}
        .s-av{width:22px;height:22px;border-radius:50%;
            background:linear-gradient(135deg,var(--green),var(--green-light));
            display:flex;align-items:center;justify-content:center;
            font-size:.58rem;font-weight:800;color:#fff;flex-shrink:0;}
        .s-seller-company{font-size:.73rem;color:var(--green);font-weight:700;}
        .s-seller-name{font-size:.73rem;color:var(--muted);}
        .s-meta{display:flex;gap:9px;flex-wrap:wrap;margin-bottom:13px;}
        .s-meta span{font-size:.7rem;color:#9ca3af;display:flex;align-items:center;gap:3px;}
        .s-foot{display:flex;align-items:center;justify-content:space-between;
            margin-top:auto;padding-top:12px;border-top:1px solid #f1f5f9;}
        .s-price{font-family:'Rubik',sans-serif;font-size:1.15rem;font-weight:800;color:var(--text);}
        .s-price .fcfa{font-size:.68rem;font-weight:600;color:var(--muted);}
        .s-price.free{color:var(--green);}
        .s-btn{background:var(--dark);color:#fff;border:none;padding:8px 16px;border-radius:50px;
            font-size:.75rem;font-weight:700;cursor:pointer;transition:all .2s;
            text-decoration:none;display:inline-flex;align-items:center;gap:5px;
            font-family:'Nunito Sans',sans-serif;}
        .s-btn:hover{background:var(--green);color:#fff;}

        /* ── CATEGORIES ── */
        .cats-sec{padding:80px 48px;background:var(--surface);}
        .cats-inner{max-width:1100px;margin:0 auto;}
        .cats-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(155px,1fr));
            gap:14px;margin-top:38px;}
        .cat-card{background:#fff;border:1.5px solid var(--border);border-radius:18px;
            padding:22px 14px;text-align:center;text-decoration:none;
            display:flex;flex-direction:column;align-items:center;gap:9px;
            transition:all .25s;cursor:pointer;}
        .cat-card:hover{background:var(--green);border-color:var(--green);
            transform:translateY(-4px);box-shadow:0 12px 28px rgba(22,163,74,.22);}
        .cat-ico{width:50px;height:50px;border-radius:14px;background:var(--green-50);
            display:flex;align-items:center;justify-content:center;font-size:1.5rem;transition:all .25s;}
        .cat-card:hover .cat-ico{background:rgba(255,255,255,.2);}
        .cat-nm{font-family:'Rubik',sans-serif;font-size:.8rem;font-weight:700;
            color:var(--text);transition:color .25s;}
        .cat-card:hover .cat-nm{color:#fff;}
        .cat-ct{font-size:.65rem;color:var(--muted-2);background:var(--surface);
            padding:2px 8px;border-radius:50px;transition:all .25s;}
        .cat-card:hover .cat-ct{background:rgba(255,255,255,.2);color:rgba(255,255,255,.8);}

        /* ── IMPACT ── */
        .impact-sec{padding:96px 48px;
            background:linear-gradient(135deg,#052e16 0%,#14532d 100%);position:relative;overflow:hidden;}
        .impact-glow{position:absolute;top:-200px;right:-200px;width:600px;height:600px;
            border-radius:50%;background:radial-gradient(circle,rgba(34,197,94,.1) 0%,transparent 70%);
            pointer-events:none;}
        .impact-inner{max-width:1100px;margin:0 auto;display:grid;
            grid-template-columns:1fr 1fr;gap:72px;align-items:center;position:relative;z-index:2;}
        .impact-left .sec-tag{color:#4ade80;}
        .impact-left .sec-line{background:#4ade80;}
        .impact-left .sec-title{color:#fff;}
        .impact-left .sec-sub{color:rgba(255,255,255,.62);}
        .impact-grid{display:grid;grid-template-columns:1fr 1fr;gap:14px;}
        .imp-card{background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1);
            border-radius:18px;padding:22px;text-align:center;transition:all .25s;}
        .imp-card:hover{background:rgba(255,255,255,.1);transform:translateY(-4px);}
        .imp-em{font-size:1.9rem;margin-bottom:10px;}
        .imp-num{font-family:'Rubik',sans-serif;font-size:2rem;font-weight:900;
            color:#4ade80;line-height:1;margin-bottom:5px;}
        .imp-lbl{font-size:.75rem;color:rgba(255,255,255,.55);}

        /* ── TESTIMONIALS ── */
        .testi-sec{padding:96px 48px;background:#fff;}
        .testi-inner{max-width:1100px;margin:0 auto;}
        .testi-hdr{text-align:center;margin-bottom:50px;}
        .testi-hdr .sec-tag{justify-content:center;}
        .testi-hdr .sec-sub{text-align:center;margin:11px auto 0;}
        .testi-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:22px;}
        .testi-card{background:var(--surface);border:1.5px solid var(--border);
            border-radius:22px;padding:26px;transition:all .25s;}
        .testi-card:hover{box-shadow:0 16px 44px rgba(0,0,0,.08);transform:translateY(-4px);border-color:transparent;}
        .t-stars{display:flex;gap:3px;margin-bottom:14px;}
        .t-stars i{color:#f59e0b;font-size:.82rem;}
        .t-quote{font-size:.88rem;color:var(--muted);line-height:1.72;margin-bottom:22px;font-style:italic;}
        .t-author{display:flex;align-items:center;gap:11px;}
        .t-av{width:42px;height:42px;border-radius:50%;
            background:linear-gradient(135deg,var(--green),var(--green-light));
            display:flex;align-items:center;justify-content:center;font-size:1.3rem;flex-shrink:0;}
        .t-name{font-family:'Rubik',sans-serif;font-size:.88rem;font-weight:700;color:var(--text);}
        .t-role{font-size:.73rem;color:var(--muted);}

        /* ── CTA ── */
        .cta-sec{padding:96px 48px;
            background:linear-gradient(135deg,var(--green) 0%,var(--green-dark) 100%);
            text-align:center;position:relative;overflow:hidden;}
        .cta-sec::before{content:'';position:absolute;top:-80px;left:50%;transform:translateX(-50%);
            width:700px;height:350px;border-radius:50%;background:rgba(255,255,255,.05);}
        .cta-sec::after{content:'';position:absolute;bottom:-120px;left:-80px;
            width:350px;height:350px;border-radius:50%;background:rgba(0,0,0,.06);}
        .cta-inner{max-width:640px;margin:0 auto;position:relative;z-index:2;}
        .cta-badge{display:inline-flex;align-items:center;gap:7px;
            background:rgba(255,255,255,.14);border:1px solid rgba(255,255,255,.28);
            color:#fff;font-size:.75rem;font-weight:700;padding:7px 16px;
            border-radius:50px;margin-bottom:26px;}
        .cta-title{font-family:'Rubik',sans-serif;font-size:clamp(2rem,4vw,3.1rem);
            font-weight:900;color:#fff;letter-spacing:-1.5px;line-height:1.1;margin-bottom:14px;}
        .cta-sub{font-size:1rem;color:rgba(255,255,255,.78);line-height:1.68;margin-bottom:34px;}
        .btn-white{display:inline-flex;align-items:center;gap:8px;background:#fff;color:var(--green);
            padding:16px 38px;border-radius:50px;font-family:'Rubik',sans-serif;font-weight:800;
            font-size:.98rem;text-decoration:none;transition:all .25s;
            box-shadow:0 4px 22px rgba(0,0,0,.14);}
        .btn-white:hover{transform:translateY(-3px);box-shadow:0 8px 36px rgba(0,0,0,.2);color:var(--green);}

        /* ── FOOTER ── */
        footer{background:#0a0a0a;color:#6b7280;padding:60px 48px 36px;}
        .footer-grid{max-width:1100px;margin:0 auto;
            display:grid;grid-template-columns:2fr 1fr 1fr 1.6fr;gap:44px;
            padding-bottom:44px;border-bottom:1px solid rgba(255,255,255,.07);}
        .f-logo{font-family:'Rubik',sans-serif;font-weight:800;font-size:1.15rem;color:#22c55e;
            margin-bottom:11px;display:flex;align-items:center;gap:8px;}
        .f-tag{font-size:.83rem;color:#6b7280;line-height:1.62;max-width:250px;margin-bottom:18px;}
        .f-social{display:flex;gap:9px;}
        .fsoc{width:34px;height:34px;border-radius:9px;background:rgba(255,255,255,.05);
            border:1px solid rgba(255,255,255,.08);display:flex;align-items:center;justify-content:center;
            color:#9ca3af;font-size:.82rem;transition:all .2s;cursor:pointer;text-decoration:none;}
        .fsoc:hover{background:var(--green);border-color:var(--green);color:#fff;}
        .f-col h5{font-family:'Rubik',sans-serif;font-size:.83rem;font-weight:700;color:#fff;
            margin-bottom:14px;letter-spacing:.4px;}
        .f-links{list-style:none;display:flex;flex-direction:column;gap:9px;}
        .f-links a{color:#6b7280;text-decoration:none;font-size:.82rem;transition:color .2s;}
        .f-links a:hover{color:#fff;}
        .nl-wrap{display:flex;gap:7px;margin-top:11px;}
        .nl-input{flex:1;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1);
            border-radius:50px;padding:10px 14px;color:#fff;font-size:.8rem;
            font-family:'Nunito Sans',sans-serif;outline:none;transition:border-color .2s;}
        .nl-input::placeholder{color:#4b5563;}
        .nl-input:focus{border-color:var(--green);}
        .nl-btn{background:var(--green);color:#fff;border:none;padding:10px 16px;
            border-radius:50px;cursor:pointer;font-size:.78rem;font-weight:700;
            transition:all .2s;font-family:'Nunito Sans',sans-serif;white-space:nowrap;}
        .nl-btn:hover{background:var(--green-dark);}
        .footer-bot{max-width:1100px;margin:0 auto;display:flex;justify-content:space-between;
            align-items:center;padding-top:28px;flex-wrap:wrap;gap:10px;}
        .f-copy{font-size:.78rem;}
        .f-legal{display:flex;gap:18px;}
        .f-legal a{font-size:.78rem;color:#6b7280;text-decoration:none;}
        .f-legal a:hover{color:#fff;}

        /* ── AUTH MODAL ── */
        .auth-overlay{display:none;position:fixed;inset:0;z-index:9999;
            background:rgba(0,0,0,.6);backdrop-filter:blur(8px);
            align-items:center;justify-content:center;padding:20px;}
        .auth-overlay.open{display:flex;animation:fadeIn .2s ease;}
        @keyframes fadeIn{from{opacity:0}to{opacity:1}}
        .auth-modal{background:#fff;border-radius:24px;width:100%;max-width:780px;
            display:grid;grid-template-columns:1fr 1fr;overflow:hidden;
            box-shadow:0 32px 80px rgba(0,0,0,.3);animation:slideUpM .25s ease;}
        @keyframes slideUpM{from{transform:translateY(22px);opacity:0}to{transform:translateY(0);opacity:1}}
        .auth-left{padding:44px 40px;display:flex;flex-direction:column;justify-content:center;}
        .auth-left h2{font-family:'Rubik',sans-serif;font-size:1.85rem;font-weight:800;
            margin-bottom:6px;letter-spacing:-.5px;}
        .auth-left .sub{font-size:.875rem;color:#888;margin-bottom:26px;line-height:1.5;}
        .auth-left .sub strong{color:var(--green);}
        .auth-input-wrap{position:relative;margin-bottom:13px;}
        .auth-input-wrap input{width:100%;padding:13px 18px;border:1.5px solid #e5e5e5;
            border-radius:50px;font-size:.9rem;outline:none;
            font-family:'Nunito Sans',sans-serif;transition:border-color .2s;}
        .auth-input-wrap input:focus{border-color:var(--green);}
        .auth-input-wrap .eye{position:absolute;right:18px;top:50%;transform:translateY(-50%);
            color:#bbb;cursor:pointer;font-size:.85rem;}
        .auth-forgot{text-align:right;font-size:.78rem;color:#888;margin-bottom:18px;
            cursor:pointer;text-decoration:none;display:block;}
        .auth-forgot:hover{color:var(--green);}
        .btn-auth-submit{width:100%;background:var(--dark);color:#fff;border:none;
            padding:14px;border-radius:50px;font-family:'Rubik',sans-serif;
            font-size:.98rem;font-weight:700;cursor:pointer;transition:all .2s;}
        .btn-auth-submit:hover{background:#222;transform:translateY(-1px);}
        .auth-divider{display:flex;align-items:center;gap:11px;margin:18px 0;}
        .auth-divider span{font-size:.75rem;color:#bbb;white-space:nowrap;}
        .auth-divider::before,.auth-divider::after{content:'';flex:1;height:1px;background:#eee;}
        .social-btns{display:flex;gap:11px;justify-content:center;margin-bottom:18px;}
        .btn-social{width:44px;height:44px;border-radius:50%;color:#fff;border:none;
            display:flex;align-items:center;justify-content:center;font-size:.95rem;
            cursor:pointer;transition:transform .15s,background .2s;}
        .btn-social:hover{transform:scale(1.1);}
        .btn-social.apple{background:#111;} .btn-social.google{background:#db4437;} .btn-social.fb{background:#1877f2;}
        .auth-switch{font-size:.82rem;color:#888;text-align:center;}
        .auth-switch a{color:var(--green);font-weight:600;text-decoration:none;}
        .auth-switch a:hover{text-decoration:underline;}
        .auth-error{font-size:.78rem;color:#dc2626;margin-bottom:10px;display:none;
            padding:9px 13px;background:#fef2f2;border-radius:10px;border:1px solid #fecaca;}
        .auth-error.show{display:block;}
        .auth-right{background:linear-gradient(135deg,#052e16,#14532d);position:relative;
            display:flex;flex-direction:column;align-items:center;justify-content:center;
            padding:40px 30px;text-align:center;overflow:hidden;}
        .auth-right::before{content:'';position:absolute;top:-80px;right:-80px;width:240px;height:240px;
            border-radius:50%;background:rgba(34,197,94,.1);}
        .auth-right::after{content:'';position:absolute;bottom:-60px;left:-60px;width:190px;height:190px;
            border-radius:50%;background:rgba(34,197,94,.06);}
        .auth-illus{font-size:5rem;margin-bottom:14px;animation:float 3s ease-in-out infinite;}
        .auth-right-title{font-family:'Rubik',sans-serif;font-size:1.15rem;font-weight:800;
            color:#fff;line-height:1.4;margin-bottom:7px;position:relative;z-index:2;}
        .auth-right-title strong{color:#4ade80;}
        .auth-right-sub{font-size:.78rem;color:rgba(255,255,255,.62);max-width:200px;
            line-height:1.6;margin-bottom:22px;position:relative;z-index:2;}
        .mini-card{background:rgba(255,255,255,.09);border:1px solid rgba(255,255,255,.14);
            border-radius:14px;padding:13px 16px;text-align:left;
            display:flex;align-items:center;gap:11px;margin-bottom:11px;width:100%;max-width:210px;
            animation:float 4s ease-in-out infinite;position:relative;z-index:2;backdrop-filter:blur(8px);}
        .mini-card-icon{font-size:1.5rem;}
        .mini-card-label{font-size:.72rem;font-weight:700;color:#fff;}
        .mini-card-sub{font-size:.67rem;color:rgba(255,255,255,.55);}
        .mini-card-prog{height:4px;background:rgba(255,255,255,.18);border-radius:4px;margin-top:5px;overflow:hidden;}
        .mini-card-fill{height:100%;background:#4ade80;border-radius:4px;width:72%;}
        .auth-avatars{display:flex;margin-top:14px;position:relative;z-index:2;}
        .auth-av{width:34px;height:34px;border-radius:50%;border:2.5px solid rgba(255,255,255,.18);
            font-size:1.05rem;background:rgba(255,255,255,.08);display:flex;align-items:center;
            justify-content:center;margin-left:-7px;}
        .auth-av:first-child{margin-left:0;}
        .auth-close{position:absolute;top:13px;right:13px;z-index:10;width:30px;height:30px;
            border-radius:50%;background:#f5f5f5;border:none;cursor:pointer;display:flex;
            align-items:center;justify-content:center;font-size:.95rem;color:#666;transition:background .2s;}
        .auth-close:hover{background:#eee;}
        .register-only{display:none;}
        .auth-modal.register-mode .register-only{display:block;}
        .auth-modal.register-mode .login-only{display:none;}

        /* ── CHAT ── */
        #chat-fab{position:fixed;bottom:26px;right:26px;z-index:9000;width:54px;height:54px;
            border-radius:50%;background:linear-gradient(135deg,var(--green),var(--green-dark));
            color:#fff;border:none;cursor:pointer;box-shadow:0 4px 20px rgba(22,163,74,.4);
            font-size:1.4rem;display:flex;align-items:center;justify-content:center;
            transition:transform .2s,box-shadow .2s;}
        #chat-fab:hover{transform:scale(1.1);box-shadow:0 6px 26px rgba(22,163,74,.5);}
        #chat-window{position:fixed;bottom:92px;right:26px;z-index:9000;width:355px;max-height:510px;
            background:#fff;border-radius:20px;box-shadow:0 8px 40px rgba(0,0,0,.18);
            display:none;flex-direction:column;overflow:hidden;border:1px solid var(--border);}
        #chat-window.open{display:flex;animation:slideUpM .25s ease;}
        #chat-header{background:linear-gradient(135deg,var(--green),var(--green-dark));
            color:#fff;padding:13px 16px;display:flex;align-items:center;justify-content:space-between;}
        #chat-header .title{font-family:'Rubik',sans-serif;font-weight:700;font-size:.92rem;}
        #chat-header .subtitle{font-size:.72rem;opacity:.84;}
        #chat-close{background:none;border:none;color:#fff;font-size:1.15rem;cursor:pointer;padding:0 3px;}
        #chat-messages{flex:1;overflow-y:auto;padding:14px;display:flex;flex-direction:column;gap:9px;min-height:190px;}
        .chat-msg{max-width:85%;padding:9px 13px;border-radius:14px;font-size:.855rem;line-height:1.5;}
        .chat-msg.user{background:var(--green);color:#fff;align-self:flex-end;border-bottom-right-radius:3px;}
        .chat-msg.bot{background:#f3f4f6;color:var(--text);align-self:flex-start;border-bottom-left-radius:3px;}
        .chat-msg.bot.typing{color:#9ca3af;font-style:italic;}
        #chat-form{display:flex;gap:7px;padding:11px 13px;border-top:1px solid #f0f0f0;}
        #chat-input{flex:1;border:1.5px solid var(--border);border-radius:11px;padding:8px 13px;
            font-size:.855rem;outline:none;font-family:'Nunito Sans',sans-serif;resize:none;transition:border-color .2s;}
        #chat-input:focus{border-color:var(--green);}
        #chat-send{background:var(--green);color:#fff;border:none;border-radius:11px;
            padding:8px 14px;cursor:pointer;font-size:.855rem;font-weight:600;transition:background .2s;}
        #chat-send:hover{background:var(--green-dark);}
        #chat-send:disabled{background:#9ca3af;cursor:not-allowed;}

        /* ── REVEAL ── */
        .reveal{opacity:0;transform:translateY(28px);transition:all .6s ease;}
        .reveal.visible{opacity:1;transform:translateY(0);}

        /* ── RESPONSIVE ── */
        @media(max-width:1024px){
            .hero-inner{grid-template-columns:1fr;text-align:center;}
            .hero-right{display:none;}
            .hero-proof{justify-content:center;}
            .hero-cta{justify-content:center;}
            .hero-sub{margin:0 auto 34px;}
            .steps{grid-template-columns:repeat(2,1fr);} .steps::before{display:none;}
            .impact-inner{grid-template-columns:1fr;gap:40px;}
            .stats-grid{grid-template-columns:repeat(2,1fr);}
            .testi-grid{grid-template-columns:repeat(2,1fr);}
            .footer-grid{grid-template-columns:1fr 1fr;}
        }
        @media(max-width:900px){
            .navbar-links{display:none;}
        }
        @media(max-width:768px){
            .navbar-custom{padding:0 16px;height:56px;}
            .hero{padding:86px 22px 60px;}
            .hero-title{font-size:2.3rem;}
            .how-sec,.shop-sec,.cats-sec,.testi-sec,.cta-sec,.impact-sec{padding:64px 20px;}
            .stats-sec{padding:36px 20px;}
            .steps{grid-template-columns:1fr;}
            .shop-grid{grid-template-columns:1fr 1fr;}
            .cats-grid{grid-template-columns:repeat(3,1fr);}
            .testi-grid{grid-template-columns:1fr;}
            .footer-grid{grid-template-columns:1fr;padding-bottom:36px;}
            footer{padding:40px 20px 28px;}
            .footer-bot{flex-direction:column;text-align:center;}
            .auth-modal{grid-template-columns:1fr;}
            .auth-right{display:none;}
            .impact-grid{grid-template-columns:1fr 1fr;}
        }
        @media(max-width:480px){
            .shop-grid{grid-template-columns:1fr;}
            .cats-grid{grid-template-columns:repeat(2,1fr);}
            .stats-grid{grid-template-columns:1fr 1fr;}
            .hero-title{font-size:1.9rem;letter-spacing:-.5px;}
            .hero-cta{flex-direction:column;gap:10px;align-items:center;}
            .hero-cta a,.hero-cta button{width:100%;max-width:320px;text-align:center;justify-content:center;}
            .how-sec,.shop-sec,.cats-sec,.testi-sec,.cta-sec,.impact-sec{padding:48px 16px;}
            .stats-sec{padding:28px 16px;}
            .sec-title{font-size:1.5rem;}
            .shop-card{border-radius:14px;}
            .stat-num{font-size:2rem;}
            .navbar-custom{padding:0 14px;}
            .navbar-actions .btn-ghost{display:none;}
            .navbar-actions .btn-dark{padding:7px 14px;font-size:.78rem;}
        }
        @media(max-width:360px){
            .hero-title{font-size:1.65rem;}
            .cats-grid{grid-template-columns:repeat(2,1fr);}
            .step-card{padding:20px 14px;}
        }
    </style>
</head>
<body>

<!-- ── NAVBAR ── -->
<nav class="navbar-custom" id="navbar">
    <div class="nav-inner">
        <a class="navbar-logo" href="{{ route('home') }}">
            <div class="logo-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M21 3C21 3 14 3 10 8C7.5 11 9 15 9 15C12 12 15 12 17 8C17 8 18 12 17 16" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M3.82 21C5.9 16.17 8 10 17 8" stroke="white" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </div>
            <span class="logo-text">
                <span class="anti">Anti</span><span class="gaspi">Gaspi</span><span class="ci">CI</span>
            </span>
        </a>
        <ul class="navbar-links">
            <li><a href="{{ route('comment-ca-marche') }}">Fonctionnement</a></li>
            <li><a href="{{ route('annonces.index') }}">Annonces</a></li>
            <li><a href="{{ route('blog') }}">Blog</a></li>
            <li><a href="{{ route('contact') }}">Contact</a></li>
        </ul>
        <div class="navbar-actions">
            @auth
                <a href="{{ route('dashboard') }}" class="btn-dark"><i class="fas fa-th-large"></i> Mon espace</a>
            @else
                <button class="btn-ghost" onclick="openAuth('login')">Connexion</button>
                <button class="btn-dark" onclick="openAuth('register')"><i class="fas fa-leaf"></i> S'inscrire</button>
            @endauth
        </div>
    </div>
</nav>

<!-- ── HERO ── -->
<section class="hero">
    <div class="hero-grid"></div>
    <div class="hero-glow-1"></div>
    <div class="hero-glow-2"></div>

    <div class="hero-inner">
        <div class="hero-left">
            <div class="hero-badge">
                <div class="badge-dot"></div>
                Côte d'Ivoire · Économie Circulaire
            </div>
            <h1 class="hero-title">
                Transformez vos<br>
                <span class="hl">surplus</span> en<br>
                <span class="hl-o">opportunités</span>
            </h1>
            <p class="hero-sub">
                AntiGaspiCI connecte restaurants, marchés et producteurs aux acheteurs — dons, ventes à prix réduit, alimentation animale. Moins de gaspillage, plus d'impact.
            </p>
            <div class="hero-cta">
                <a href="{{ route('inscription') }}" class="btn-orange">
                    <i class="fas fa-rocket"></i> Commencer gratuitement
                </a>
                <a href="{{ route('annonces.index') }}" class="btn-glass">
                    <i class="fas fa-search"></i> Explorer les annonces
                </a>
            </div>
            <div class="hero-proof">
                <div class="proof-avs">
                    <div class="proof-av" style="background:linear-gradient(135deg,#7c3aed,#a855f7)">👨🏿‍🌾</div>
                    <div class="proof-av" style="background:linear-gradient(135deg,#dc2626,#f87171)">👩🏾‍🍳</div>
                    <div class="proof-av" style="background:linear-gradient(135deg,#2563eb,#60a5fa)">🧑🏽‍💼</div>
                    <div class="proof-av" style="background:linear-gradient(135deg,#d97706,#fbbf24)">👩🏿</div>
                </div>
                <div class="proof-txt">
                    <strong>+500 membres</strong> déjà actifs<br>en Côte d'Ivoire
                </div>
            </div>
        </div>

        <div class="hero-right">
            <div class="notif-float">
                <div class="nf-ico">🎉</div>
                <div>
                    <div class="nf-title">Nouvelle réservation</div>
                    <div class="nf-sub">Mangues · Adjamé</div>
                </div>
            </div>
            <div class="phone">
                <div class="phone-notch"></div>
                <div class="pcard">
                    <div class="pcard-top">
                        <div class="pcard-ico" style="background:#dcfce7">🍎</div>
                        <span class="pbadge don">Don gratuit</span>
                    </div>
                    <div class="pcard-title">Cageots de mangues fraîches</div>
                    <div class="pcard-sub">Marché Adjamé · 15 kg</div>
                    <div class="pcard-foot">
                        <span class="pprice free">Gratuit</span>
                        <span class="ploc"><i class="fas fa-map-marker-alt"></i> Adjamé</span>
                    </div>
                </div>
                <div class="pcard">
                    <div class="pcard-top">
                        <div class="pcard-ico" style="background:#fef9c3">🍞</div>
                        <span class="pbadge vente">Vente</span>
                    </div>
                    <div class="pcard-title">Pain invendu du jour</div>
                    <div class="pcard-sub">Boulangerie Plateau · 8 unités</div>
                    <div class="pcard-foot">
                        <span class="pprice paid">500 FCFA</span>
                        <span class="ploc"><i class="fas fa-map-marker-alt"></i> Plateau</span>
                    </div>
                </div>
                <div class="pcard">
                    <div class="pcard-top">
                        <div class="pcard-ico" style="background:#ffe4e6">🥛</div>
                        <span class="pbadge urgent"><i class="fas fa-fire"></i> Urgent · 3h</span>
                    </div>
                    <div class="pcard-title">Lait frais excédentaire</div>
                    <div class="pcard-sub">Ferme Tiassalé · 20 L</div>
                    <div class="pcard-foot">
                        <span class="pprice paid">1 200 FCFA</span>
                        <span class="ploc"><i class="fas fa-map-marker-alt"></i> Yopougon</span>
                    </div>
                </div>
            </div>
            <div class="impact-pill">
                <div class="ip-ico">🌱</div>
                <div class="ip-txt">
                    <strong>{{ number_format($co2EviteTonnes, 1, ',', ' ') }} tonnes</strong>de CO2 évité au total
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ── TICKER ── -->
<div class="ticker">
    <div class="ticker-track">
        {{-- Categories reelles (table categories), repetees 2x pour le defilement en boucle --}}
        @foreach($allCats->concat($allCats) as $cat)
            <span class="t-item"><span class="t-dot"></span>{{ $cat->nom }}</span>
        @endforeach
    </div>
</div>

<!-- ── STATS ── -->
<section class="stats-sec">
    @php /* stats injectees par WelcomeController */ @endphp
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-ico">📦</div>
            <div class="stat-num" data-target="{{ $nbAnnonces }}">{{ $nbAnnonces }}</div>
            <div class="stat-lbl">Annonces actives</div>
        </div>
        <div class="stat-card">
            <div class="stat-ico">🏪</div>
            <div class="stat-num" data-target="{{ $nbFournisseurs }}">{{ $nbFournisseurs }}</div>
            <div class="stat-lbl">Fournisseurs inscrits</div>
        </div>
        <div class="stat-card">
            <div class="stat-ico">🤝</div>
            <div class="stat-num" data-target="{{ $nbEchanges }}">{{ $nbEchanges }}</div>
            <div class="stat-lbl">Échanges réalisés</div>
        </div>
        <div class="stat-card">
            <div class="stat-ico">🗂️</div>
            <div class="stat-num" data-target="{{ $nbCategories }}">{{ $nbCategories }}</div>
            <div class="stat-lbl">Catégories</div>
        </div>
    </div>
</section>

<!-- ── HOW IT WORKS ── -->
<section class="how-sec">
    <div class="how-inner">
        <div class="how-hdr reveal">
            <div class="sec-tag"><div class="sec-line"></div>Fonctionnement</div>
            <h2 class="sec-title">Simple, rapide, <span class="hl">efficace.</span></h2>
            <p class="sec-sub">De l'inscription à l'échange en quelques minutes. Une plateforme pensée pour la Côte d'Ivoire.</p>
        </div>
        <div class="steps">
            <div class="step reveal">
                <div class="step-bub" style="background:#dbeafe">
                    <i class="fas fa-user-plus" style="color:#2563eb"></i>
                    <div class="step-n">1</div>
                </div>
                <div class="step-t">S'inscrire</div>
                <p class="step-d">Créez votre compte gratuit — fournisseur (restaurant, marché, producteur) ou acheteur (particulier, association, éleveur).</p>
            </div>
            <div class="step reveal">
                <div class="step-bub" style="background:#dcfce7">
                    <i class="fas fa-bullhorn" style="color:#16a34a"></i>
                    <div class="step-n">2</div>
                </div>
                <div class="step-t">Publier</div>
                <p class="step-d">Publiez vos surplus en temps réel : photo, quantité, prix, localisation. Visible instantanément sur la plateforme.</p>
            </div>
            <div class="step reveal">
                <div class="step-bub" style="background:#fef9c3">
                    <i class="fas fa-search" style="color:#d97706"></i>
                    <div class="step-n">3</div>
                </div>
                <div class="step-t">Trouver</div>
                <p class="step-d">Cherchez par catégorie, ville ou type d'offre. Les alertes personnalisées vous préviennent en temps réel.</p>
            </div>
            <div class="step reveal">
                <div class="step-bub" style="background:#f3e8ff">
                    <i class="fas fa-handshake" style="color:#9333ea"></i>
                    <div class="step-n">4</div>
                </div>
                <div class="step-t">Échanger</div>
                <p class="step-d">Réservez, discutez via la messagerie intégrée et organisez la collecte. Simple, sécurisé et traçable.</p>
            </div>
        </div>
    </div>
</section>

<!-- ── BOUTIQUE ── -->
@php /* shopAnnonces injecte par WelcomeController */ @endphp
<section class="shop-sec">
    <div class="shop-inner">
        <div class="shop-hdr reveal">
            <div>
                <div class="sec-tag"><div class="sec-line"></div>Boutique en ligne</div>
                <h2 class="sec-title">Surplus disponibles <span class="hl">maintenant</span></h2>
            </div>
            <a href="{{ route('annonces.index') }}" class="btn-all">
                Tout voir <i class="fas fa-arrow-right"></i>
            </a>
        </div>
        <div class="shop-grid">
            @forelse($shopAnnonces as $annonce)
            @php
                $tCss   = ['vente'=>'type-vente','don'=>'type-don','alimentation_animale'=>'type-animal','transformation'=>'type-transform'];
                $tLabel = ['vente'=>'Vente','don'=>'Don gratuit','alimentation_animale'=>'Animal','transformation'=>'Transformation'];
                $isUrgent = method_exists($annonce,'estUrgent') && $annonce->estUrgent();
            @endphp
            <div class="shop-card reveal">
                <div class="s-img">
                    <img src="{{ $annonce->image_url }}" alt="{{ $annonce->titre }}" style="width:100%;height:100%;object-fit:cover;" onerror="this.src='{{ $annonce->default_image_by_category }}';">
                    <span class="s-type {{ $tCss[$annonce->type_offre] ?? 'type-don' }}">{{ $tLabel[$annonce->type_offre] ?? $annonce->type_offre }}</span>
                    @if($isUrgent)<span class="s-urgent"><i class="fas fa-fire"></i> {{ $annonce->heuresRestantes() }}h</span>@endif
                </div>
                <div class="s-body">
                    <div class="s-title">{{ Str::limit($annonce->titre,45) }}</div>
                    <div class="s-seller">
                        <div class="s-av">{{ strtoupper(substr($annonce->user->prenom??'V',0,1)) }}</div>
                        <div>
                            @if($annonce->user->nom_structure)<div class="s-seller-company"><i class="fas fa-store"></i> {{ $annonce->user->nom_structure }}</div>@endif
                            <div class="s-seller-name">{{ $annonce->user->prenom??'—' }} {{ $annonce->user->nom??'' }}</div>
                        </div>
                    </div>
                    <div class="s-meta">
                        <span><i class="fas fa-map-marker-alt"></i> {{ Str::limit($annonce->adresse_collecte??'Abidjan',18) }}</span>
                        <span><i class="fas fa-weight"></i> {{ $annonce->quantite }} {{ $annonce->unite }}</span>
                        @if($annonce->categorie)<span><i class="fas fa-tag"></i> {{ $annonce->categorie->nom }}</span>@endif
                    </div>
                    <div class="s-foot">
                        @if($annonce->type_offre==='don')
                            <div class="s-price free">Gratuit</div>
                        @elseif($annonce->prix>0)
                            <div class="s-price">{{ number_format($annonce->prix,0,',',' ') }} <span class="fcfa">FCFA</span></div>
                        @else
                            <div class="s-price" style="color:#9ca3af;font-size:.88rem;">À discuter</div>
                        @endif
                        <a href="{{ route('annonces.show',$annonce) }}" class="s-btn">Voir <i class="fas fa-arrow-right"></i></a>
                    </div>
                </div>
            </div>
            @empty
            @foreach([
                ['e'=>'🍎','t'=>'Cageots de mangues fraîches','v'=>'Marché Adjamé','p'=>'Kouamé','tc'=>'type-vente','tl'=>'Vente','pr'=>'3 500','l'=>'Adjamé','q'=>'15 kg','c'=>'Fruits & Légumes','u'=>false],
                ['e'=>'🍞','t'=>'Pain invendu du jour','v'=>'Boulangerie du Plateau','p'=>'Aya','tc'=>'type-don','tl'=>'Don gratuit','pr'=>null,'l'=>'Plateau','q'=>'8 unités','c'=>'Pain & Pâtisserie','u'=>true],
                ['e'=>'🥛','t'=>'Lait frais excédentaire','v'=>'Ferme Tiassalé','p'=>'Moussa','tc'=>'type-vente','tl'=>'Vente','pr'=>'1 200','l'=>'Yopougon','q'=>'20 L','c'=>'Produits Laitiers','u'=>false],
                ['e'=>'🌾','t'=>'Céréales invendues après récolte','v'=>'Coopérative Abobo','p'=>'Fatou','tc'=>'type-animal','tl'=>'Animal','pr'=>'800','l'=>'Abobo','q'=>'50 kg','c'=>'Céréales','u'=>false],
                ['e'=>'🍊','t'=>'Oranges fraîches en surplus','v'=>'Producteur Bouaké','p'=>'Koffi','tc'=>'type-vente','tl'=>'Vente','pr'=>'2 000','l'=>'Cocody','q'=>'30 kg','c'=>'Fruits & Légumes','u'=>false],
                ['e'=>'🥩','t'=>'Découpes de bœuf fraîches','v'=>'Boucherie Treichville','p'=>'Ibrahim','tc'=>'type-vente','tl'=>'Vente','pr'=>'5 500','l'=>'Treichville','q'=>'8 kg','c'=>'Viande','u'=>true],
                ['e'=>'🫙','t'=>'Conserves de tomates artisanales','v'=>'Atelier Yopougon','p'=>'Marie','tc'=>'type-transform','tl'=>'Transformation','pr'=>'1 800','l'=>'Yopougon','q'=>'24 unités','c'=>'Épicerie','u'=>false],
                ['e'=>'🧃','t'=>'Jus de gingembre artisanal','v'=>'Atelier Bingerville','p'=>'Aminata','tc'=>'type-vente','tl'=>'Vente','pr'=>'1 200','l'=>'Bingerville','q'=>'12 L','c'=>'Boissons','u'=>false],
            ] as $ex)
            <div class="shop-card reveal">
                <div class="s-img">
                    <div class="s-emoji">{{ $ex['e'] }}</div>
                    <span class="s-type {{ $ex['tc'] }}">{{ $ex['tl'] }}</span>
                    @if($ex['u'])<span class="s-urgent"><i class="fas fa-fire"></i> 3h</span>@endif
                </div>
                <div class="s-body">
                    <div class="s-title">{{ $ex['t'] }}</div>
                    <div class="s-seller">
                        <div class="s-av">{{ strtoupper(substr($ex['p'],0,1)) }}</div>
                        <div>
                            <div class="s-seller-company"><i class="fas fa-store"></i> {{ $ex['v'] }}</div>
                            <div class="s-seller-name">{{ $ex['p'] }}</div>
                        </div>
                    </div>
                    <div class="s-meta">
                        <span><i class="fas fa-map-marker-alt"></i> {{ $ex['l'] }}</span>
                        <span><i class="fas fa-weight"></i> {{ $ex['q'] }}</span>
                        <span><i class="fas fa-tag"></i> {{ $ex['c'] }}</span>
                    </div>
                    <div class="s-foot">
                        @if($ex['pr'])<div class="s-price">{{ $ex['pr'] }} <span class="fcfa">FCFA</span></div>
                        @else<div class="s-price free">Gratuit</div>@endif
                        <a href="#" onclick="openAuth('login');return false;" class="s-btn">Voir <i class="fas fa-arrow-right"></i></a>
                    </div>
                </div>
            </div>
            @endforeach
            @endforelse
        </div>
    </div>
</section>

<!-- ── CATEGORIES ── -->
<section class="cats-sec">
    <div class="cats-inner">
        <div class="reveal">
            <div class="sec-tag"><div class="sec-line"></div>Catégories</div>
            <h2 class="sec-title">Explorer par <span class="hl">catégorie</span></h2>
            <p class="sec-sub">Trouvez exactement ce que vous cherchez parmi toutes les catégories de surplus alimentaires disponibles.</p>
        </div>
        <div class="cats-grid">
            @foreach($allCats as $cat)
            <a href="{{ route('annonces.index',['categorie'=>$cat->id]) }}" class="cat-card reveal">
                <div class="cat-ico">{{ $cat->icone }}</div>
                <div class="cat-nm">{{ $cat->nom }}</div>
                <div class="cat-ct">{{ $cat->annonces_count }} offres</div>
            </a>
            @endforeach
        </div>
    </div>
</section>

<!-- ── IMPACT ── -->
<section class="impact-sec">
    <div class="impact-glow"></div>
    <div class="impact-inner">
        <div class="impact-left reveal">
            <div class="sec-tag"><div class="sec-line"></div>Notre Impact</div>
            <h2 class="sec-title">Ensemble, on fait<br><span style="color:#4ade80">la différence.</span></h2>
            <p class="sec-sub">Chaque échange sur AntiGaspiCI contribue à réduire les émissions de CO2 et à nourrir plus de familles en Côte d'Ivoire.</p>
            <a href="{{ route('inscription') }}" class="btn-orange" style="margin-top:26px;display:inline-flex;">
                Rejoindre le mouvement <i class="fas fa-arrow-right"></i>
            </a>
        </div>
        <div class="impact-grid">
            @foreach([
                ['e'=>'⚖️','n'=>number_format($co2EviteTonnes, 1, ',', ' ').' t','l'=>'de CO2 évité (estimation)'],
                ['e'=>'🌾','n'=>number_format($kgSauves, 0, ',', ' ').' kg','l'=>'d\'aliments sauvés'],
                ['e'=>'🍽️','n'=>number_format($repasEquivalents, 0, ',', ' '),'l'=>'repas équivalents (estimation)'],
                ['e'=>'💰','n'=>number_format($fcfaEconomises, 0, ',', ' ').' F','l'=>'valorisés en FCFA'],
            ] as $imp)
            <div class="imp-card reveal">
                <div class="imp-em">{{ $imp['e'] }}</div>
                <div class="imp-num">{{ $imp['n'] }}</div>
                <div class="imp-lbl">{{ $imp['l'] }}</div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- ── TESTIMONIALS ── -->
<section class="testi-sec">
    <div class="testi-inner">
        <div class="testi-hdr reveal">
            <div class="sec-tag" style="justify-content:center"><div class="sec-line"></div>Témoignages<div class="sec-line"></div></div>
            <h2 class="sec-title">Ce qu'ils <span class="hl">disent</span></h2>
            <p class="sec-sub">Des membres actifs partagent leur expérience avec AntiGaspiCI.</p>
        </div>
        <div class="testi-grid">
            <div class="testi-card reveal">
                <div class="t-stars">@for($i=0;$i<5;$i++)<i class="fas fa-star"></i>@endfor</div>
                <p class="t-quote">"Grâce à AntiGaspiCI, je valorise mes invendus du soir au lieu de les jeter. En 3 mois, j'ai économisé plus de 80 000 FCFA et touché de nouveaux clients."</p>
                <div class="t-author">
                    <div class="t-av">👩🏾‍🍳</div>
                    <div>
                        <div class="t-name">Aya Konan</div>
                        <div class="t-role">Restauratrice · Plateau, Abidjan</div>
                    </div>
                </div>
            </div>
            <div class="testi-card reveal">
                <div class="t-stars">@for($i=0;$i<5;$i++)<i class="fas fa-star"></i>@endfor</div>
                <p class="t-quote">"Je trouve chaque semaine des surplus de légumes frais pour mon association. Les familles que nous accompagnons bénéficient de vrais repas équilibrés."</p>
                <div class="t-author">
                    <div class="t-av" style="background:linear-gradient(135deg,#2563eb,#60a5fa)">🧑🏾‍💼</div>
                    <div>
                        <div class="t-name">Kouassi Mensah</div>
                        <div class="t-role">Directeur ONG · Yopougon</div>
                    </div>
                </div>
            </div>
            <div class="testi-card reveal">
                <div class="t-stars">@for($i=0;$i<5;$i++)<i class="fas fa-star"></i>@endfor</div>
                <p class="t-quote">"Mes céréales invendues après la récolte alimentent maintenant 3 élevages de la région. Avant, je perdais tout. Maintenant j'ai un revenu complémentaire fiable."</p>
                <div class="t-author">
                    <div class="t-av" style="background:linear-gradient(135deg,#d97706,#fbbf24)">👨🏿‍🌾</div>
                    <div>
                        <div class="t-name">Moussa Coulibaly</div>
                        <div class="t-role">Agriculteur · Bouaké</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ── CTA ── -->
<section class="cta-sec">
    <div class="cta-inner">
        <div class="cta-badge"><i class="fas fa-leaf"></i> Rejoignez le mouvement</div>
        <h2 class="cta-title">Moins de gaspillage.<br>Plus d'impact.</h2>
        <p class="cta-sub">Des centaines de fournisseurs et d'acheteurs échangent leurs surplus chaque jour en Côte d'Ivoire. Inscrivez-vous gratuitement et rejoignez-les.</p>
        <a href="{{ route('inscription') }}" class="btn-white">
            <i class="fas fa-rocket"></i> Créer mon compte gratuit
        </a>
    </div>
</section>

<!-- ── FOOTER ── -->
<footer>
    <div class="footer-grid">
        <div>
            <div class="f-logo">🌿 AntiGaspiCI</div>
            <p class="f-tag">La plateforme collaborative de réduction du gaspillage alimentaire en Côte d'Ivoire. Connectons surplus et besoins.</p>
            <div class="f-social">
                <a href="#" class="fsoc"><i class="fab fa-facebook-f"></i></a>
                <a href="#" class="fsoc"><i class="fab fa-instagram"></i></a>
                <a href="#" class="fsoc"><i class="fab fa-twitter"></i></a>
                <a href="#" class="fsoc"><i class="fab fa-whatsapp"></i></a>
            </div>
        </div>
        <div class="f-col">
            <h5>Plateforme</h5>
            <ul class="f-links">
                <li><a href="{{ route('annonces.index') }}">Annonces</a></li>
                <li><a href="{{ route('comment-ca-marche') }}">Fonctionnement</a></li>
                <li><a href="{{ route('blog') }}">Blog</a></li>
                <li><a href="{{ route('contact') }}">Contact</a></li>
            </ul>
        </div>
        <div class="f-col">
            <h5>Mon compte</h5>
            <ul class="f-links">
                <li><a href="#" onclick="openAuth('login')">Connexion</a></li>
                <li><a href="{{ route('inscription') }}">Inscription</a></li>
                @auth
                <li><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li><a href="{{ route('annonces.create') }}">Publier</a></li>
                @endauth
            </ul>
        </div>
        <div class="f-col">
            <h5>Alertes & Actus</h5>
            <p style="font-size:.8rem;line-height:1.62;margin-bottom:10px;color:#6b7280;">Recevez les meilleures offres près de chez vous.</p>
            @if(session('newsletter_success'))
                <p style="font-size:.78rem;color:#22c55e;font-weight:700;margin-bottom:8px;">{{ session('newsletter_success') }}</p>
            @endif
            <form class="nl-wrap" action="{{ route('newsletter.store') }}" method="POST">
                @csrf
                <input type="email" name="email" class="nl-input" placeholder="votre@email.com" required>
                <button type="submit" class="nl-btn">OK</button>
            </form>
        </div>
    </div>
    <div class="footer-bot">
        <div class="f-copy">© {{ date('Y') }} AntiGaspiCI — Côte d'Ivoire. Tous droits réservés.</div>
        <div class="f-legal">
            <a href="{{ route('mentions-legales') }}">Mentions légales</a>
            <a href="{{ route('confidentialite') }}">Confidentialité</a>
            <a href="{{ route('cgu') }}">CGU</a>
        </div>
    </div>
</footer>

<!-- ── AUTH MODAL ── -->
<div class="auth-overlay" id="authOverlay" onclick="closeAuthIfBg(event)">
    <div class="auth-modal" id="authModal">
        <button class="auth-close" onclick="closeAuth()">✕</button>
        <div class="auth-left">
            <div id="loginForm">
                <h2>Bon retour !</h2>
                <p class="sub">Simplifiez vos échanges et réduisez le gaspillage<br>avec <strong>AntiGaspiCI</strong>.</p>
                @if(session('error'))<div class="auth-error show">{{ session('error') }}</div>@endif
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
                    <a href="{{ route('social.redirect','google') }}" class="btn-social google" title="Google"><i class="fab fa-google"></i></a>
                    <span class="btn-social apple" title="Apple (bientôt)" style="cursor:not-allowed;opacity:.5"><i class="fab fa-apple"></i></span>
                    <a href="{{ route('social.redirect','facebook') }}" class="btn-social fb" title="Facebook"><i class="fab fa-facebook-f"></i></a>
                </div>
                <p class="auth-switch">Pas encore membre ? <a href="#" onclick="switchToRegister()">S'inscrire</a></p>
            </div>
            <div id="registerForm" style="display:none">
                <h2>Créer un compte</h2>
                <p class="sub">Rejoignez des centaines de membres sur <strong>AntiGaspiCI</strong>.</p>
                @if($errors->hasAny(['nom','prenom','email','password','role','telephone']))
                    <div class="auth-error show">@foreach($errors->all() as $e)<div>• {{ $e }}</div>@endforeach</div>
                @else
                    <div class="auth-error" id="registerError"></div>
                @endif
                <form action="{{ route('inscrire') }}" method="POST">
                    @csrf
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:9px">
                        <div class="auth-input-wrap"><input type="text" name="prenom" placeholder="Prénom" required value="{{ old('prenom') }}"></div>
                        <div class="auth-input-wrap"><input type="text" name="nom" placeholder="Nom" required value="{{ old('nom') }}"></div>
                    </div>
                    <div class="auth-input-wrap"><input type="email" name="email" placeholder="Adresse e-mail" required value="{{ old('email') }}" style="{{ $errors->has('email')?'border-color:#dc2626':'' }}"></div>
                    <div class="auth-input-wrap"><input type="password" name="password" placeholder="Mot de passe (min. 8 car.)" required style="{{ $errors->has('password')?'border-color:#dc2626':'' }}"></div>
                    <div class="auth-input-wrap"><input type="password" name="password_confirmation" placeholder="Confirmer le mot de passe" required></div>
                    <div style="display:flex;gap:14px;margin-bottom:13px">
                        <label style="display:flex;align-items:center;gap:6px;cursor:pointer;font-size:.84rem;font-weight:600">
                            <input type="radio" name="role" value="acheteur" {{ old('role','acheteur')==='acheteur'?'checked':'' }}> Acheteur
                        </label>
                        <label style="display:flex;align-items:center;gap:6px;cursor:pointer;font-size:.84rem;font-weight:600">
                            <input type="radio" name="role" value="fournisseur" {{ old('role')==='fournisseur'?'checked':'' }}> Fournisseur
                        </label>
                    </div>
                    <input type="hidden" name="telephone" value="">
                    <button type="submit" class="btn-auth-submit">Créer mon compte</button>
                </form>
                <p class="auth-switch" style="margin-top:14px">Déjà membre ? <a href="#" onclick="switchToLogin()">Se connecter</a></p>
            </div>
        </div>
        <div class="auth-right">
            <div class="auth-illus">🌿</div>
            <div class="auth-right-title">Réduis le gaspillage,<br><strong>maximise l'impact</strong></div>
            <p class="auth-right-sub">Des surplus transformés en opportunités chaque jour en Côte d'Ivoire.</p>
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
                <div class="auth-av">👩🏿</div>
            </div>
        </div>
    </div>
</div>

<!-- ── CHAT ── -->
<button id="chat-fab" onclick="toggleChat()" title="Assistant AntiGaspiCI">💬</button>
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
// Navbar scroll
window.addEventListener('scroll',()=>{
    document.getElementById('navbar').classList.toggle('scrolled',window.scrollY>50);
});

// Scroll reveal
const revealObs = new IntersectionObserver(entries=>{
    entries.forEach(e=>{ if(e.isIntersecting) e.target.classList.add('visible'); });
},{threshold:.08,rootMargin:'0px 0px -30px 0px'});
document.querySelectorAll('.reveal').forEach(el=>revealObs.observe(el));

// Auth modal
function openAuth(mode){
    document.getElementById('authOverlay').classList.add('open');
    document.body.style.overflow='hidden';
    mode==='register'?switchToRegister():switchToLogin();
}
function closeAuth(){
    document.getElementById('authOverlay').classList.remove('open');
    document.body.style.overflow='';
}
function closeAuthIfBg(e){ if(e.target===document.getElementById('authOverlay')) closeAuth(); }
function switchToRegister(){ document.getElementById('loginForm').style.display='none'; document.getElementById('registerForm').style.display='block'; }
function switchToLogin(){ document.getElementById('registerForm').style.display='none'; document.getElementById('loginForm').style.display='block'; }
function togglePwd(){
    const p=document.getElementById('pwdInput'),i=document.getElementById('eyeIcon');
    p.type=p.type==='password'?'text':'password';
    i.className=p.type==='password'?'fas fa-eye':'fas fa-eye-slash';
}
document.addEventListener('keydown',e=>{ if(e.key==='Escape') closeAuth(); });

@if($errors->hasAny(['nom','prenom','role','telephone'])||($errors->hasAny(['email','password'])&&old('prenom')))
    document.addEventListener('DOMContentLoaded',()=>openAuth('register'));
@elseif($errors->any()||session('error'))
    document.addEventListener('DOMContentLoaded',()=>openAuth('login'));
@endif

// Chat widget
(function(){
    const history=[];let isOpen=false;
    window.toggleChat=function(){
        isOpen=!isOpen;
        const w=document.getElementById('chat-window'),f=document.getElementById('chat-fab');
        w.classList.toggle('open',isOpen);
        f.textContent=isOpen?'✕':'💬';
        if(isOpen) document.getElementById('chat-input').focus();
    };
    window.sendMessage=async function(e){
        e.preventDefault();
        const input=document.getElementById('chat-input');
        const text=input.value.trim();
        if(!text) return;
        appendMsg('user',text);
        history.push({role:'user',content:text});
        input.value='';input.style.height='auto';
        const btn=document.getElementById('chat-send');btn.disabled=true;
        const botEl=appendMsg('bot','',true);
        try{
            const res=await fetch('{{ route("chat") }}',{
                method:'POST',
                headers:{'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]')?.content??'{{ csrf_token() }}'},
                body:JSON.stringify({message:text,history:history.slice(0,-1)})
            });
            if(!res.ok) throw new Error();
            const reader=res.body.getReader(),dec=new TextDecoder();
            let full='',buf='';
            while(true){
                const{done,value}=await reader.read();if(done) break;
                buf+=dec.decode(value,{stream:true});
                const lines=buf.split('\n');buf=lines.pop();
                for(const line of lines){
                    if(!line.startsWith('data: ')) continue;
                    const raw=line.slice(6).trim();
                    if(raw==='[DONE]') break;
                    try{const d=JSON.parse(raw);if(d.text){full+=d.text;botEl.textContent=full;botEl.classList.remove('typing');scrollChat();}else if(d.error){botEl.textContent='⚠️ '+d.error;botEl.classList.remove('typing');botEl.style.color='#ef4444';scrollChat();}}catch{}
                }
            }
            if(full) history.push({role:'assistant',content:full});
        }catch{botEl.textContent="Désolé, une erreur s'est produite. Réessayez.";botEl.style.color='#ef4444';}
        finally{btn.disabled=false;input.focus();}
    };
    function appendMsg(role,text,typing=false){
        const msgs=document.getElementById('chat-messages');
        const el=document.createElement('div');
        el.className='chat-msg '+role+(typing?' typing':'');
        el.textContent=typing?'…':text;
        msgs.appendChild(el);scrollChat();return el;
    }
    function scrollChat(){const m=document.getElementById('chat-messages');m.scrollTop=m.scrollHeight;}
    document.getElementById('chat-input').addEventListener('input',function(){
        this.style.height='auto';this.style.height=Math.min(this.scrollHeight,120)+'px';
    });
})();
</script>

</body>
</html>
