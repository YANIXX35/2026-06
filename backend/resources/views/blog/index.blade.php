<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Blog Santé & Alimentation — AntiGaspiCI</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css"/>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:'Inter',sans-serif; background:#f8fafc; color:#111; }

        /* NAVBAR */
        .navbar {
            position:fixed; top:0; left:0; right:0; z-index:100;
            display:flex; align-items:center; justify-content:space-between;
            padding:0 48px; height:64px;
            background:rgba(255,255,255,.95); backdrop-filter:blur(12px);
            border-bottom:1px solid #f0f0f0;
        }
        .navbar-logo { font-weight:800; font-size:1.1rem; color:#16a34a; text-decoration:none; }
        .navbar-links { display:flex; gap:28px; list-style:none; }
        .navbar-links a { text-decoration:none; color:#555; font-size:.875rem; font-weight:500; transition:color .2s; }
        .navbar-links a:hover, .navbar-links a.active { color:#16a34a; }
        .navbar-cta { background:#111; color:#fff; border:none; padding:9px 22px; border-radius:50px; font-size:.85rem; font-weight:700; cursor:pointer; text-decoration:none; }

        /* HERO */
        .hero {
            margin-top:64px; padding:56px 0 40px;
            background:linear-gradient(135deg,#f0fdf4 0%,#dcfce7 100%);
            text-align:center;
        }
        .hero-tag { display:inline-block; background:#16a34a; color:#fff; font-size:.7rem; font-weight:700; padding:4px 12px; border-radius:50px; letter-spacing:.08em; text-transform:uppercase; margin-bottom:16px; }
        .hero h1 { font-size:2.2rem; font-weight:800; color:#111; margin-bottom:12px; }
        .hero p { color:#555; font-size:1rem; max-width:520px; margin:0 auto 24px; }
        .hero-meta { display:flex; gap:24px; justify-content:center; align-items:center; flex-wrap:wrap; }
        .hero-stat { font-size:.8rem; color:#666; display:flex; align-items:center; gap:6px; }
        .hero-stat strong { color:#16a34a; font-weight:700; }

        /* SOURCES */
        .sources-bar {
            background:#fff; border-bottom:1px solid #f0f0f0;
            padding:12px 48px; display:flex; gap:10px; align-items:center; flex-wrap:wrap;
        }
        .source-pill {
            display:inline-flex; align-items:center; gap:6px;
            padding:5px 14px; border-radius:50px; border:1.5px solid #e5e7eb;
            font-size:.78rem; font-weight:600; cursor:pointer; transition:all .2s;
            background:#fff; color:#555; text-decoration:none;
        }
        .source-pill:hover, .source-pill.active { border-color:currentColor; }
        .source-pill .dot { width:8px; height:8px; border-radius:50%; }
        .sources-label { font-size:.78rem; color:#999; font-weight:600; margin-right:4px; }

        /* MAIN */
        .main { max-width:1200px; margin:0 auto; padding:40px 24px 80px; }

        /* GRID */
        .blog-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:24px; margin-bottom:40px; }
        @media(max-width:900px){ .blog-grid{ grid-template-columns:repeat(2,1fr); } }
        @media(max-width:580px){ .blog-grid{ grid-template-columns:1fr; } }

        /* CARD */
        .blog-card {
            background:#fff; border-radius:16px; overflow:hidden;
            box-shadow:0 1px 4px rgba(0,0,0,.06); border:1px solid #f0f0f0;
            transition:transform .2s, box-shadow .2s; display:flex; flex-direction:column;
        }
        .blog-card:hover { transform:translateY(-3px); box-shadow:0 8px 28px rgba(0,0,0,.1); }

        .card-img {
            width:100%; height:180px; object-fit:cover;
            background:linear-gradient(135deg,#f0fdf4,#dcfce7);
        }
        .card-img-placeholder {
            width:100%; height:180px;
            display:flex; align-items:center; justify-content:center;
            font-size:2.5rem; background:linear-gradient(135deg,#f0fdf4,#dcfce7);
        }
        .card-body { padding:18px; flex:1; display:flex; flex-direction:column; }
        .card-source {
            display:inline-flex; align-items:center; gap:5px;
            font-size:.7rem; font-weight:700; padding:3px 10px; border-radius:50px;
            color:#fff; margin-bottom:10px; width:fit-content;
        }
        .card-title { font-size:.9rem; font-weight:700; color:#111; line-height:1.4; margin-bottom:8px; }
        .card-desc { font-size:.78rem; color:#666; line-height:1.6; flex:1; margin-bottom:12px; }
        .card-footer-row { display:flex; align-items:center; justify-content:space-between; }
        .card-date { font-size:.7rem; color:#aaa; display:flex; align-items:center; gap:4px; }
        .card-link {
            font-size:.75rem; font-weight:700; color:#16a34a; text-decoration:none;
            display:flex; align-items:center; gap:4px; transition:gap .2s;
        }
        .card-link:hover { gap:7px; }

        /* FEATURED (première carte) */
        .blog-card.featured { grid-column:1/-1; flex-direction:row; }
        .blog-card.featured .card-img { width:380px; height:auto; flex-shrink:0; }
        .blog-card.featured .card-img-placeholder { width:380px; height:auto; min-height:220px; flex-shrink:0; }
        .blog-card.featured .card-title { font-size:1.15rem; }
        .blog-card.featured .card-desc { font-size:.85rem; }
        @media(max-width:700px){ .blog-card.featured{ flex-direction:column; } .blog-card.featured .card-img, .blog-card.featured .card-img-placeholder { width:100%; height:200px; } }

        /* PAGINATION */
        .pagination-wrap { display:flex; justify-content:center; gap:8px; flex-wrap:wrap; margin-top:32px; }
        .page-btn {
            padding:8px 16px; border-radius:10px; border:1.5px solid #e5e7eb;
            background:#fff; color:#555; font-size:.8rem; font-weight:600;
            cursor:pointer; text-decoration:none; transition:all .2s;
        }
        .page-btn:hover { border-color:#16a34a; color:#16a34a; }
        .page-btn.active { background:#16a34a; color:#fff; border-color:#16a34a; }
        .page-btn.disabled { color:#ccc; cursor:default; pointer-events:none; }

        /* REFRESH */
        .refresh-btn {
            background:none; border:1.5px solid #e5e7eb; border-radius:10px;
            padding:6px 14px; font-size:.78rem; color:#666; cursor:pointer;
            display:flex; align-items:center; gap:6px; transition:all .2s;
        }
        .refresh-btn:hover { border-color:#16a34a; color:#16a34a; }

        /* FLASH */
        .flash { background:#f0fdf4; border:1px solid #bbf7d0; color:#166534; padding:10px 18px; border-radius:10px; margin-bottom:20px; font-size:.85rem; }

        /* EMPTY */
        .empty { text-align:center; padding:60px 20px; color:#999; }
        .empty .icon { font-size:3rem; margin-bottom:12px; }
    </style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar">
    <a href="{{ route('home') }}" class="navbar-logo">🌿 AntiGaspiCI</a>
    <ul class="navbar-links">
        <li><a href="{{ route('home') }}">Accueil</a></li>
        <li><a href="{{ route('annonces.index') }}">Annonces</a></li>
        <li><a href="{{ route('blog') }}" class="active">Blog</a></li>
        <li><a href="{{ route('comment-ca-marche') }}">Fonctionnement</a></li>
    </ul>
    @auth
        <a href="{{ route('dashboard') }}" class="navbar-cta">Mon espace</a>
    @else
        <a href="{{ route('connexion') }}" class="navbar-cta">Connexion</a>
    @endauth
</nav>

<!-- HERO -->
<section class="hero">
    <div class="hero-tag">Blog</div>
    <h1>Santé & Alimentation</h1>
    <p>Les dernières actualités sur la nutrition, la santé et l'alimentation durable, agrégées en temps réel.</p>
    <div class="hero-meta">
        <div class="hero-stat"><i class="fas fa-rss" style="color:#16a34a;"></i> <strong>4</strong> sources RSS</div>
        <div class="hero-stat"><i class="fas fa-newspaper" style="color:#16a34a;"></i> <strong>{{ count($articles) }}</strong> articles</div>
        <div class="hero-stat"><i class="fas fa-sync-alt" style="color:#16a34a;"></i> Mis à jour toutes les heures</div>
    </div>
</section>

<!-- SOURCES BAR -->
<div class="sources-bar">
    <span class="sources-label">Sources :</span>
    <a href="{{ route('blog') }}" class="source-pill active" id="pill-all">
        Toutes
    </a>
    @foreach([['OMS','#1d4ed8'],['Le Monde Santé','#dc2626'],['Futura Santé','#7c3aed'],['Santé Magazine','#059669']] as [$nom,$couleur])
    <a href="{{ route('blog') }}?source={{ urlencode($nom) }}" class="source-pill" style="color:{{ $couleur }}" id="pill-{{ Str::slug($nom) }}">
        <span class="dot" style="background:{{ $couleur }}"></span>{{ $nom }}
    </a>
    @endforeach

    <div style="margin-left:auto;">
        @auth
        <form action="{{ route('blog.refresh') }}" method="POST" style="display:inline;">
            @csrf
            <button class="refresh-btn"><i class="fas fa-sync-alt"></i> Actualiser</button>
        </form>
        @endauth
    </div>
</div>

<!-- MAIN -->
<div class="main">

    @if(session('success'))
    <div class="flash">✅ {{ session('success') }}</div>
    @endif

    @php
        $source = request('source');
        $filtered = $source ? array_filter($articles, fn($a) => $a['source'] === $source) : $articles;
        $filtered  = array_values($filtered);

        $perPage  = 12;
        $page     = max(1, (int) request('page', 1));
        $total    = count($filtered);
        $pages    = (int) ceil($total / $perPage);
        $offset   = ($page - 1) * $perPage;
        $current  = array_slice($filtered, $offset, $perPage);
    @endphp

    @if(empty($current))
    <div class="empty">
        <div class="icon">📭</div>
        <p>Aucun article disponible pour le moment.</p>
        <p style="font-size:.8rem;margin-top:8px;">Les flux RSS seront rechargés dans moins d'une heure.</p>
    </div>
    @else
    <div class="blog-grid">
        @foreach($current as $i => $article)
        <article class="blog-card {{ $i === 0 && $page === 1 && !$source ? 'featured' : '' }}">
            @if($article['image'])
                <img src="{{ $article['image'] }}" alt="" class="card-img" loading="lazy" onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                <div class="card-img-placeholder" style="display:none;">{{ $article['icone'] }}</div>
            @else
                <div class="card-img-placeholder">{{ $article['icone'] }}</div>
            @endif
            <div class="card-body">
                <span class="card-source" style="background:{{ $article['couleur'] }}">
                    {{ $article['icone'] }} {{ $article['source'] }}
                </span>
                <div class="card-title">{{ $article['titre'] }}</div>
                @if($article['description'])
                <div class="card-desc">{{ $article['description'] }}</div>
                @endif
                <div class="card-footer-row">
                    <span class="card-date"><i class="fas fa-calendar-alt"></i>{{ $article['date_fmt'] }}</span>
                    <a href="{{ $article['lien'] }}" target="_blank" rel="noopener" class="card-link">
                        Lire <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </article>
        @endforeach
    </div>

    @if($pages > 1)
    <div class="pagination-wrap">
        @if($page > 1)
        <a href="?{{ http_build_query(array_merge(request()->except('page'), ['page' => $page - 1])) }}" class="page-btn">‹ Préc.</a>
        @else
        <span class="page-btn disabled">‹ Préc.</span>
        @endif

        @for($p = max(1, $page - 2); $p <= min($pages, $page + 2); $p++)
        <a href="?{{ http_build_query(array_merge(request()->except('page'), ['page' => $p])) }}"
           class="page-btn {{ $p === $page ? 'active' : '' }}">{{ $p }}</a>
        @endfor

        @if($page < $pages)
        <a href="?{{ http_build_query(array_merge(request()->except('page'), ['page' => $page + 1])) }}" class="page-btn">Suiv. ›</a>
        @else
        <span class="page-btn disabled">Suiv. ›</span>
        @endif
    </div>
    <p style="text-align:center;font-size:.75rem;color:#aaa;margin-top:12px;">
        Page {{ $page }} sur {{ $pages }} — {{ $total }} articles
    </p>
    @endif

    @endif
</div>

<!-- Footer simple -->
<footer style="text-align:center;padding:24px;color:#aaa;font-size:.8rem;border-top:1px solid #f0f0f0;">
    © {{ date('Y') }} AntiGaspiCI — Sources : OMS, Le Monde Santé, Futura Sciences, Santé Magazine
</footer>

<script>
// Surligne la source active
const source = new URLSearchParams(window.location.search).get('source');
if (source) {
    document.getElementById('pill-all')?.classList.remove('active');
    const slug = source.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '');
    document.getElementById('pill-' + slug)?.classList.add('active');
}
</script>

</body>
</html>
