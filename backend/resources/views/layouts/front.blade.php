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
            position:fixed;top:14px;left:50%;transform:translateX(-50%);
            z-index:200;width:calc(100% - 48px);max-width:1180px;
            display:flex;align-items:center;justify-content:space-between;
            padding:0 22px;height:56px;
            background:rgba(255,255,255,.96);backdrop-filter:blur(20px);
            border:1px solid rgba(255,255,255,.9);border-radius:50px;
            box-shadow:0 4px 24px rgba(0,0,0,.07);transition:box-shadow .3s;
        }
        .front-nav.scrolled{box-shadow:0 8px 32px rgba(0,0,0,.12);}
        .fn-logo{font-family:'Rubik',sans-serif;font-weight:800;font-size:1.06rem;
            color:var(--green);text-decoration:none;display:flex;align-items:center;gap:8px;}
        .fn-logo-ico{width:28px;height:28px;border-radius:8px;
            background:linear-gradient(135deg,var(--green),var(--green-light));
            display:flex;align-items:center;justify-content:center;font-size:.85rem;}
        .fn-logo span{color:var(--text);}
        .fn-links{display:flex;gap:2px;list-style:none;}
        .fn-links a{text-decoration:none;color:var(--muted);font-size:.85rem;font-weight:600;
            padding:6px 12px;border-radius:50px;transition:all .2s;}
        .fn-links a:hover,.fn-links a.active{color:var(--text);background:var(--surface);}
        .fn-actions{display:flex;gap:7px;align-items:center;}
        .btn-ghost{background:none;border:none;color:var(--text);font-weight:600;font-size:.85rem;
            padding:7px 15px;border-radius:50px;cursor:pointer;transition:all .2s;
            font-family:'Nunito Sans',sans-serif;text-decoration:none;display:inline-block;}
        .btn-ghost:hover{background:var(--surface);}
        .btn-dark{background:var(--dark);color:#fff;border:none;padding:8px 20px;
            border-radius:50px;font-size:.85rem;font-weight:700;cursor:pointer;
            transition:all .2s;font-family:'Nunito Sans',sans-serif;
            text-decoration:none;display:inline-flex;align-items:center;gap:6px;}
        .btn-dark:hover{background:#222;transform:translateY(-1px);color:#fff;}
        .btn-green{background:var(--green);color:#fff;border:none;padding:8px 20px;
            border-radius:50px;font-size:.85rem;font-weight:700;cursor:pointer;
            transition:all .2s;font-family:'Nunito Sans',sans-serif;
            text-decoration:none;display:inline-flex;align-items:center;gap:6px;}
        .btn-green:hover{background:var(--green-dark);transform:translateY(-1px);color:#fff;}
        .notif-btn{position:relative;width:36px;height:36px;border-radius:50%;
            background:var(--surface);border:1px solid var(--border);
            display:flex;align-items:center;justify-content:center;
            cursor:pointer;color:var(--muted);font-size:.85rem;transition:all .2s;
            text-decoration:none;}
        .notif-btn:hover{background:var(--green-50);color:var(--green);}
        .notif-badge{position:absolute;top:-3px;right:-3px;background:#ef4444;color:#fff;
            font-size:.55rem;font-weight:800;border-radius:50%;width:16px;height:16px;
            display:flex;align-items:center;justify-content:center;border:2px solid #fff;}

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
        @media(max-width:768px){
            .front-nav{width:calc(100% - 28px);padding:0 16px;}
            .fn-links{display:none;}
            .page-hero{padding:100px 20px 48px;}
            .front-footer{padding:40px 20px 24px;}
            .ff-grid{grid-template-columns:1fr;}
            .ff-bot{flex-direction:column;text-align:center;}
        }
    </style>
</head>
<body>

<nav class="front-nav" id="frontNav">
    <a class="fn-logo" href="{{ route('home') }}">
        <div class="fn-logo-ico">🌿</div>
        <span>Anti</span>GaspiCI
    </a>
    <ul class="fn-links">
        <li><a href="{{ route('comment-ca-marche') }}" class="{{ request()->is('comment-ca-marche') ? 'active':'' }}">Fonctionnement</a></li>
        <li><a href="{{ route('annonces.index') }}" class="{{ request()->is('annonces*') ? 'active':'' }}">Annonces</a></li>
        <li><a href="{{ route('blog') }}" class="{{ request()->is('blog') ? 'active':'' }}">Blog</a></li>
        <li><a href="{{ route('contact') }}" class="{{ request()->is('contact') ? 'active':'' }}">Contact</a></li>
    </ul>
    <div class="fn-actions">
        @auth
            @php $unread = Auth::user()->unreadNotifications->count(); @endphp
            <a href="{{ route('notifications.index') }}" class="notif-btn" title="Notifications">
                <i class="fas fa-bell"></i>
                @if($unread > 0)<span class="notif-badge">{{ $unread > 9 ? '9+' : $unread }}</span>@endif
            </a>
            <a href="{{ route('dashboard') }}" class="btn-dark"><i class="fas fa-th-large"></i> Mon espace</a>
        @else
            <a href="{{ route('connexion') }}" class="btn-ghost">Connexion</a>
            <a href="{{ route('inscription') }}" class="btn-dark">S'inscrire</a>
        @endauth
    </div>
</nav>

@yield('content')

<footer class="front-footer">
    <div class="ff-grid">
        <div>
            <div class="ff-logo">🌿 AntiGaspiCI</div>
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
