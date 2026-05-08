@extends('layouts.front')
@section('title','Blog Santé & Alimentation')
@section('description','Actualités nutrition, santé et alimentation durable — agrégées en temps réel depuis les meilleures sources.')

@push('styles')
<style>
/* ── SOURCES BAR ── */
.sources-bar{
    background:#fff;border-bottom:1px solid var(--border);
    padding:14px 48px;display:flex;gap:10px;align-items:center;flex-wrap:wrap;
    position:sticky;top:82px;z-index:100;
}
.sources-label{font-size:.72rem;font-weight:800;color:var(--muted-2);text-transform:uppercase;
    letter-spacing:1.5px;margin-right:4px;}
.source-pill{
    display:inline-flex;align-items:center;gap:6px;padding:6px 15px;
    border-radius:50px;border:1.5px solid var(--border);font-size:.76rem;
    font-weight:700;cursor:pointer;transition:all .2s;background:#fff;
    color:var(--muted);text-decoration:none;
}
.source-pill:hover{border-color:currentColor;}
.source-pill.active{border-color:currentColor;background:var(--surface);}
.source-dot{width:7px;height:7px;border-radius:50%;flex-shrink:0;}
.refresh-btn{margin-left:auto;background:none;border:1.5px solid var(--border);border-radius:50px;
    padding:7px 16px;font-size:.76rem;color:var(--muted);cursor:pointer;font-family:'Nunito Sans',sans-serif;
    display:flex;align-items:center;gap:5px;transition:all .2s;}
.refresh-btn:hover{border-color:var(--green);color:var(--green);}

/* ── MAIN ── */
.blog-section{padding:56px 48px 80px;background:var(--surface);}
.blog-inner{max-width:1100px;margin:0 auto;}

/* ── FEATURED ── */
.featured-card{
    display:grid;grid-template-columns:420px 1fr;gap:0;
    background:#fff;border-radius:20px;border:1px solid var(--border);
    overflow:hidden;margin-bottom:32px;transition:all .3s;
}
.featured-card:hover{box-shadow:0 16px 48px rgba(0,0,0,.1);transform:translateY(-3px);}
.featured-img{width:100%;height:100%;object-fit:cover;min-height:260px;display:block;}
.featured-placeholder{width:100%;min-height:260px;display:flex;align-items:center;
    justify-content:center;font-size:4rem;
    background:linear-gradient(135deg,var(--green-50),var(--green-100));}
.featured-body{padding:36px 32px;display:flex;flex-direction:column;justify-content:center;}
.featured-badge{display:inline-flex;align-items:center;gap:6px;font-size:.68rem;
    font-weight:800;padding:4px 12px;border-radius:50px;color:#fff;margin-bottom:14px;width:fit-content;}
.featured-tag{display:inline-block;background:var(--green-50);color:var(--green);
    font-size:.66rem;font-weight:800;padding:3px 10px;border-radius:50px;
    letter-spacing:.5px;text-transform:uppercase;margin-bottom:10px;}
.featured-title{font-family:'Rubik',sans-serif;font-size:1.35rem;font-weight:900;
    color:var(--text);line-height:1.3;margin-bottom:10px;}
.featured-desc{font-size:.86rem;color:var(--muted);line-height:1.7;margin-bottom:20px;flex:1;}
.featured-foot{display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;}
.featured-date{font-size:.73rem;color:var(--muted-2);display:flex;align-items:center;gap:5px;}
.btn-read{background:var(--green);color:#fff;padding:9px 22px;border-radius:50px;
    font-size:.8rem;font-weight:700;text-decoration:none;display:inline-flex;align-items:center;gap:6px;
    transition:all .2s;}
.btn-read:hover{background:var(--green-dark);color:#fff;transform:translateY(-1px);}

/* ── GRID ── */
.blog-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:20px;}
.blog-card{
    background:#fff;border-radius:16px;border:1px solid var(--border);
    overflow:hidden;display:flex;flex-direction:column;transition:all .3s;
}
.blog-card:hover{transform:translateY(-4px);box-shadow:0 12px 36px rgba(0,0,0,.08);}
.bc-img{width:100%;height:168px;object-fit:cover;display:block;}
.bc-placeholder{width:100%;height:168px;display:flex;align-items:center;justify-content:center;
    font-size:2.8rem;background:linear-gradient(135deg,var(--green-50),var(--green-100));}
.bc-body{padding:18px 18px 14px;flex:1;display:flex;flex-direction:column;}
.bc-source{display:inline-flex;align-items:center;gap:5px;font-size:.67rem;font-weight:800;
    padding:3px 10px;border-radius:50px;color:#fff;margin-bottom:10px;width:fit-content;}
.bc-title{font-family:'Rubik',sans-serif;font-size:.9rem;font-weight:800;color:var(--text);
    line-height:1.4;margin-bottom:7px;}
.bc-desc{font-size:.78rem;color:var(--muted);line-height:1.65;flex:1;margin-bottom:12px;}
.bc-foot{display:flex;align-items:center;justify-content:space-between;border-top:1px solid var(--border);padding-top:10px;}
.bc-date{font-size:.7rem;color:var(--muted-2);display:flex;align-items:center;gap:4px;}
.bc-link{font-size:.74rem;font-weight:700;color:var(--green);text-decoration:none;
    display:flex;align-items:center;gap:4px;transition:gap .2s;}
.bc-link:hover{gap:8px;}

/* ── PAGINATION ── */
.pag-wrap{display:flex;justify-content:center;align-items:center;gap:6px;margin-top:40px;flex-wrap:wrap;}
.pag-btn{width:38px;height:38px;border-radius:50%;border:1.5px solid var(--border);
    background:#fff;color:var(--text);font-size:.82rem;font-weight:600;
    text-decoration:none;display:flex;align-items:center;justify-content:center;
    transition:all .2s;cursor:pointer;}
.pag-btn:hover{border-color:var(--green);color:var(--green);}
.pag-btn.active{background:var(--green);border-color:var(--green);color:#fff;}
.pag-btn.disabled{opacity:.35;pointer-events:none;}
.pag-info{font-size:.76rem;color:var(--muted);padding:0 8px;}

/* ── EMPTY ── */
.empty-state{text-align:center;padding:80px 24px;background:#fff;border-radius:16px;border:1px solid var(--border);}
.empty-state .empty-ico{font-size:3rem;margin-bottom:14px;display:block;}
.empty-state h4{font-family:'Rubik',sans-serif;font-size:1.05rem;font-weight:800;color:var(--text);margin-bottom:6px;}
.empty-state p{font-size:.84rem;color:var(--muted);}

/* ── FLASH ── */
.flash-ok{background:#f0fdf4;border:1px solid #bbf7d0;color:#15803d;
    border-radius:12px;padding:13px 18px;margin-bottom:24px;
    display:flex;align-items:center;gap:9px;font-size:.86rem;font-weight:600;}

@media(max-width:900px){
    .featured-card{grid-template-columns:1fr;}
    .featured-img,.featured-placeholder{min-height:220px;}
    .blog-grid{grid-template-columns:repeat(2,1fr);}
}
@media(max-width:768px){
    .sources-bar{padding:12px 16px;top:auto;position:static;}
    .blog-section{padding:36px 16px 60px;}
    .blog-grid{grid-template-columns:1fr;}
    .featured-body{padding:22px 18px;}
}
</style>
@endpush

@section('content')

{{-- HERO --}}
<div class="page-hero">
    <div class="page-hero-grid"></div>
    <div class="page-hero-glow"></div>
    <div class="page-hero-inner">
        <div class="page-hero-tag">
            <svg width="8" height="8" viewBox="0 0 8 8"><circle cx="4" cy="4" r="4" fill="#4ade80"/></svg>
            Mis à jour toutes les heures
        </div>
        <h1>Blog <span>Santé</span> &amp; Alimentation</h1>
        <p>Actualités nutrition, alimentation durable et santé — agrégées en temps réel depuis les meilleures sources internationales.</p>
    </div>
    <svg class="page-hero-wave" viewBox="0 0 1440 56" xmlns="http://www.w3.org/2000/svg">
        <path d="M0,32 C360,56 1080,0 1440,32 L1440,56 L0,56 Z" fill="#f8fafc"/>
    </svg>
</div>

{{-- SOURCES BAR --}}
<div class="sources-bar">
    <span class="sources-label">Sources :</span>
    <a href="{{ route('blog') }}" class="source-pill" id="pill-all">Toutes</a>
    @foreach([['OMS','#1d4ed8'],['Le Monde Santé','#dc2626'],['Futura Santé','#7c3aed'],['Santé Magazine','#059669']] as [$nom,$couleur])
    <a href="{{ route('blog') }}?source={{ urlencode($nom) }}" class="source-pill" style="color:{{ $couleur }};" id="pill-{{ Str::slug($nom) }}">
        <span class="source-dot" style="background:{{ $couleur }};"></span>{{ $nom }}
    </a>
    @endforeach
    @auth
    <form action="{{ route('blog.refresh') }}" method="POST" style="margin-left:auto;">
        @csrf
        <button class="refresh-btn"><i class="fas fa-sync-alt"></i> Actualiser les flux</button>
    </form>
    @endauth
</div>

{{-- MAIN --}}
<section class="blog-section">
    <div class="blog-inner">

        @if(session('success'))
        <div class="flash-ok"><i class="fas fa-check-circle"></i>{{ session('success') }}</div>
        @endif

        @php
            $source   = request('source');
            $filtered = $source ? array_values(array_filter($articles, fn($a) => $a['source'] === $source)) : $articles;
            $perPage  = 12;
            $page     = max(1, (int) request('page', 1));
            $total    = count($filtered);
            $pages    = max(1, (int) ceil($total / $perPage));
            $offset   = ($page - 1) * $perPage;
            $current  = array_slice($filtered, $offset, $perPage);
        @endphp

        @if(empty($current))
        <div class="empty-state">
            <span class="empty-ico">📭</span>
            <h4>Aucun article disponible</h4>
            <p>Les flux RSS seront rechargés dans moins d'une heure.</p>
        </div>
        @else

        {{-- FEATURED (1er article, page 1, pas de filtre source) --}}
        @if($page === 1 && !$source && isset($current[0]))
        @php $featured = $current[0]; $rest = array_slice($current, 1); @endphp
        <div class="featured-card reveal">
            @if($featured['image'])
                <img src="{{ $featured['image'] }}" alt="" class="featured-img" loading="eager"
                     onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                <div class="featured-placeholder" style="display:none;">{{ $featured['icone'] }}</div>
            @else
                <div class="featured-placeholder">{{ $featured['icone'] }}</div>
            @endif
            <div class="featured-body">
                <span class="featured-tag">À la une</span>
                <span class="featured-badge" style="background:{{ $featured['couleur'] }};">
                    {{ $featured['icone'] }} {{ $featured['source'] }}
                </span>
                <h2 class="featured-title">{{ $featured['titre'] }}</h2>
                @if($featured['description'])
                <p class="featured-desc">{{ $featured['description'] }}</p>
                @endif
                <div class="featured-foot">
                    <div class="featured-date"><i class="fas fa-calendar-alt"></i>{{ $featured['date_fmt'] }}</div>
                    <a href="{{ $featured['lien'] }}" target="_blank" rel="noopener" class="btn-read">
                        Lire l'article <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
        @else
        @php $rest = $current; @endphp
        @endif

        {{-- GRID --}}
        @if(!empty($rest))
        <div class="blog-grid">
            @foreach($rest as $article)
            <article class="blog-card reveal">
                @if($article['image'])
                    <img src="{{ $article['image'] }}" alt="" class="bc-img" loading="lazy"
                         onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                    <div class="bc-placeholder" style="display:none;">{{ $article['icone'] }}</div>
                @else
                    <div class="bc-placeholder">{{ $article['icone'] }}</div>
                @endif
                <div class="bc-body">
                    <span class="bc-source" style="background:{{ $article['couleur'] }};">
                        {{ $article['icone'] }} {{ $article['source'] }}
                    </span>
                    <div class="bc-title">{{ $article['titre'] }}</div>
                    @if($article['description'])
                    <div class="bc-desc">{{ Str::limit($article['description'], 110) }}</div>
                    @endif
                    <div class="bc-foot">
                        <span class="bc-date"><i class="fas fa-calendar-alt"></i>{{ $article['date_fmt'] }}</span>
                        <a href="{{ $article['lien'] }}" target="_blank" rel="noopener" class="bc-link">
                            Lire <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </article>
            @endforeach
        </div>
        @endif

        {{-- PAGINATION --}}
        @if($pages > 1)
        <div class="pag-wrap">
            @if($page > 1)
                <a href="?{{ http_build_query(array_merge(request()->except('page'),['page'=>$page-1])) }}" class="pag-btn">‹</a>
            @else
                <span class="pag-btn disabled">‹</span>
            @endif

            @for($p = max(1,$page-2); $p <= min($pages,$page+2); $p++)
                <a href="?{{ http_build_query(array_merge(request()->except('page'),['page'=>$p])) }}"
                   class="pag-btn {{ $p===$page?'active':'' }}">{{ $p }}</a>
            @endfor

            @if($page < $pages)
                <a href="?{{ http_build_query(array_merge(request()->except('page'),['page'=>$page+1])) }}" class="pag-btn">›</a>
            @else
                <span class="pag-btn disabled">›</span>
            @endif

            <span class="pag-info">Page {{ $page }}/{{ $pages }} — {{ $total }} articles</span>
        </div>
        @endif

        @endif
    </div>
</section>

@endsection

@push('scripts')
<script>
// Highlight active source pill
(function(){
    const src = new URLSearchParams(window.location.search).get('source');
    if(src){
        document.getElementById('pill-all')?.classList.remove('active');
        const slug = src.toLowerCase().replace(/[^a-z0-9]+/g,'-').replace(/(^-|-$)/g,'');
        document.getElementById('pill-'+slug)?.classList.add('active');
    } else {
        document.getElementById('pill-all')?.classList.add('active');
    }
})();
</script>
@endpush
