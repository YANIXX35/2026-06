@extends('layouts.front')
@section('title','Annonces')
@section('description','Parcourez toutes les annonces anti-gaspillage alimentaire en Côte d\'Ivoire.')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<style>
/* ── SEARCH HERO ── */
.search-hero{
    background:linear-gradient(135deg,#052e16 0%,#0d3d1f 50%,#0f5132 100%);
    padding:110px 48px 80px;position:relative;overflow:hidden;
}
.search-hero-grid{position:absolute;inset:0;
    background-image:linear-gradient(rgba(255,255,255,.025) 1px,transparent 1px),
        linear-gradient(90deg,rgba(255,255,255,.025) 1px,transparent 1px);
    background-size:52px 52px;pointer-events:none;}
.search-hero-glow{position:absolute;top:-80px;right:-80px;width:360px;height:360px;border-radius:50%;
    background:radial-gradient(circle,rgba(34,197,94,.12) 0%,transparent 70%);pointer-events:none;}
.search-hero-inner{position:relative;z-index:2;max-width:720px;margin:0 auto;text-align:center;}
.search-hero h1{font-family:'Rubik',sans-serif;font-size:clamp(2rem,4vw,2.8rem);
    font-weight:900;color:#fff;letter-spacing:-1.5px;margin-bottom:8px;}
.search-hero h1 span{background:linear-gradient(135deg,#4ade80,#22c55e);
    -webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;}
.search-hero p{color:rgba(255,255,255,.65);font-size:.95rem;margin-bottom:28px;}

.search-bar{
    display:flex;gap:0;background:rgba(255,255,255,.08);border:1.5px solid rgba(255,255,255,.18);
    border-radius:50px;overflow:hidden;max-width:560px;margin:0 auto;backdrop-filter:blur(10px);
}
.search-bar input{flex:1;background:none;border:none;padding:13px 20px;color:#fff;
    font-size:.9rem;font-family:'Nunito Sans',sans-serif;outline:none;}
.search-bar input::placeholder{color:rgba(255,255,255,.45);}
.search-bar button{background:var(--green);color:#fff;border:none;padding:13px 24px;
    font-weight:700;font-size:.85rem;cursor:pointer;transition:background .2s;
    font-family:'Nunito Sans',sans-serif;display:flex;align-items:center;gap:6px;}
.search-bar button:hover{background:var(--green-dark);}

.stats-pills{display:flex;justify-content:center;gap:16px;margin-top:22px;flex-wrap:wrap;}
.stat-pill{display:inline-flex;align-items:center;gap:7px;
    background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.15);
    color:rgba(255,255,255,.75);font-size:.75rem;font-weight:600;
    padding:6px 14px;border-radius:50px;}
.stat-pill strong{color:#4ade80;}

.search-hero-wave{position:absolute;bottom:-1px;left:0;right:0;pointer-events:none;}

/* ── MAIN LAYOUT ── */
.ann-section{padding:48px;background:#f8fafc;}
.ann-inner{max-width:1100px;margin:0 auto;display:grid;grid-template-columns:260px 1fr;gap:28px;align-items:start;}

/* ── FILTER SIDEBAR ── */
.filter-sidebar{position:sticky;top:82px;background:#fff;border-radius:18px;
    border:1px solid var(--border);overflow:hidden;}
.filter-head{background:linear-gradient(135deg,var(--green),var(--green-dark));
    padding:16px 20px;display:flex;align-items:center;gap:8px;}
.filter-head h5{font-family:'Rubik',sans-serif;font-size:.88rem;font-weight:800;color:#fff;margin:0;}
.filter-body{padding:20px;}
.filter-group{margin-bottom:18px;}
.filter-label{font-size:.68rem;font-weight:800;color:var(--muted);text-transform:uppercase;
    letter-spacing:1.5px;margin-bottom:10px;display:block;}
.filter-sep{border:none;border-top:1px solid var(--border);margin:16px 0;}

.radio-item{display:flex;align-items:center;gap:9px;padding:5px 0;cursor:pointer;}
.radio-item input[type="radio"]{accent-color:var(--green);width:15px;height:15px;cursor:pointer;}
.radio-item label{font-size:.82rem;color:var(--text);cursor:pointer;line-height:1.3;}
.radio-item input:checked + label{color:var(--green);font-weight:700;}

.filter-text{width:100%;border:1.5px solid var(--border);border-radius:10px;
    padding:9px 12px;font-size:.82rem;font-family:'Nunito Sans',sans-serif;
    color:var(--text);outline:none;transition:border-color .2s;}
.filter-text:focus{border-color:var(--green);}

.btn-filter{width:100%;background:var(--green);color:#fff;border:none;padding:11px;
    border-radius:50px;font-weight:700;font-size:.82rem;cursor:pointer;transition:all .2s;
    font-family:'Nunito Sans',sans-serif;display:flex;align-items:center;justify-content:center;gap:6px;}
.btn-filter:hover{background:var(--green-dark);}
.btn-reset{width:100%;background:var(--surface);color:var(--muted);border:1px solid var(--border);
    padding:9px;border-radius:50px;font-size:.8rem;font-weight:600;cursor:pointer;
    transition:all .2s;margin-top:7px;font-family:'Nunito Sans',sans-serif;text-decoration:none;
    display:flex;align-items:center;justify-content:center;gap:6px;}
.btn-reset:hover{background:var(--border);color:var(--text);}

/* ── ANNONCES LIST ── */
.ann-list{min-width:0;}
.ann-top-bar{display:flex;justify-content:space-between;align-items:center;margin-bottom:18px;flex-wrap:wrap;gap:10px;}
.ann-count{font-family:'Rubik',sans-serif;font-size:.95rem;font-weight:800;color:var(--text);}
.ann-count span{display:inline-flex;align-items:center;justify-content:center;
    background:var(--green);color:#fff;font-size:.75rem;font-weight:800;
    width:26px;height:26px;border-radius:50%;margin-right:6px;}
.sort-sel{border:1.5px solid var(--border);border-radius:50px;padding:7px 14px;
    font-size:.8rem;font-family:'Nunito Sans',sans-serif;color:var(--text);
    background:#fff;outline:none;cursor:pointer;transition:border-color .2s;}
.sort-sel:focus{border-color:var(--green);}

/* ── MAP TOGGLE ── */
.map-toggle-row{display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;}
.map-toggle-row span{font-size:.8rem;color:var(--muted);}
.btn-map-toggle{display:flex;align-items:center;gap:7px;background:#fff;border:1.5px solid var(--border);
    color:var(--text);padding:7px 16px;border-radius:50px;font-size:.8rem;font-weight:700;
    cursor:pointer;transition:all .2s;font-family:'Nunito Sans',sans-serif;}
.btn-map-toggle:hover,.btn-map-toggle.active{background:var(--green);border-color:var(--green);color:#fff;}
#mapWrapper{margin-bottom:18px;border-radius:16px;overflow:hidden;border:1px solid var(--border);}
#annoncesMap{height:380px;}
.leaflet-popup-content{font-size:.8rem;line-height:1.5;min-width:180px;}
.leaflet-popup-content a{color:var(--green);font-weight:600;text-decoration:none;}

/* ── ANNONCE CARD ── */
.ann-card{
    background:#fff;border-radius:16px;border:1px solid var(--border);
    overflow:hidden;display:flex;margin-bottom:16px;
    transition:all .3s;
}
.ann-card:hover{box-shadow:0 12px 36px rgba(0,0,0,.09);transform:translateY(-2px);}
.ann-img{width:200px;flex-shrink:0;position:relative;overflow:hidden;}
.ann-img img{width:100%;height:100%;object-fit:cover;transition:transform .4s;}
.ann-card:hover .ann-img img{transform:scale(1.06);}
.ann-img-placeholder{width:100%;height:100%;background:var(--surface);
    display:flex;align-items:center;justify-content:center;min-height:160px;}
.ann-img-placeholder i{font-size:2rem;color:var(--muted-2);}
.ann-badge{position:absolute;top:10px;left:10px;font-size:.65rem;font-weight:800;
    padding:4px 10px;border-radius:50px;color:#fff;letter-spacing:.3px;}
.ann-urgent{position:absolute;top:10px;right:10px;background:rgba(239,68,68,.92);
    color:#fff;font-size:.62rem;font-weight:800;padding:3px 8px;border-radius:50px;
    backdrop-filter:blur(4px);}
.ann-body{flex:1;padding:20px 22px;display:flex;flex-direction:column;justify-content:space-between;}
.ann-cat{font-size:.7rem;font-weight:700;padding:3px 9px;border-radius:50px;
    background:var(--surface);border:1px solid var(--border);color:var(--muted);
    display:inline-block;margin-bottom:7px;}
.ann-title{font-family:'Rubik',sans-serif;font-size:1rem;font-weight:800;color:var(--text);
    margin-bottom:4px;line-height:1.3;}
.ann-title a{color:inherit;text-decoration:none;transition:color .2s;}
.ann-title a:hover{color:var(--green);}
.ann-desc{font-size:.82rem;color:var(--muted);line-height:1.6;margin-bottom:10px;}
.ann-price{font-family:'Rubik',sans-serif;font-size:1.15rem;font-weight:900;}
.ann-price.vente{color:var(--green);}
.ann-price.gratuit{color:#059669;}
.ann-meta{display:flex;flex-wrap:wrap;gap:12px;font-size:.75rem;color:var(--muted);margin-top:8px;}
.ann-meta span{display:flex;align-items:center;gap:4px;}
.ann-meta i{color:var(--green);font-size:.7rem;}
.ann-actions{display:flex;gap:8px;margin-top:12px;flex-wrap:wrap;}
.btn-ann-primary{background:var(--green);color:#fff;border:none;padding:8px 18px;
    border-radius:50px;font-size:.78rem;font-weight:700;text-decoration:none;
    display:inline-flex;align-items:center;gap:5px;transition:all .2s;cursor:pointer;}
.btn-ann-primary:hover{background:var(--green-dark);color:#fff;transform:translateY(-1px);}
.btn-ann-outline{background:transparent;color:var(--green);border:1.5px solid var(--green-100);
    padding:7px 16px;border-radius:50px;font-size:.78rem;font-weight:700;
    text-decoration:none;display:inline-flex;align-items:center;gap:5px;transition:all .2s;}
.btn-ann-outline:hover{background:var(--green-50);border-color:var(--green);color:var(--green);}

/* ── EMPTY STATE ── */
.empty-state{text-align:center;padding:64px 24px;background:#fff;border-radius:16px;border:1px solid var(--border);}
.empty-state i{font-size:2.5rem;color:var(--muted-2);margin-bottom:16px;display:block;}
.empty-state h4{font-family:'Rubik',sans-serif;font-size:1.1rem;font-weight:800;color:var(--text);margin-bottom:6px;}
.empty-state p{font-size:.85rem;color:var(--muted);margin-bottom:20px;}

/* ── PAGINATION ── */
.pag-wrap{display:flex;justify-content:center;margin-top:24px;}
.pag-wrap .pagination{display:flex;gap:4px;list-style:none;flex-wrap:wrap;}
.pag-wrap .page-item .page-link{display:flex;align-items:center;justify-content:center;
    width:36px;height:36px;border-radius:50%;border:1.5px solid var(--border);
    color:var(--text);font-size:.82rem;font-weight:600;text-decoration:none;transition:all .2s;}
.pag-wrap .page-item.active .page-link{background:var(--green);border-color:var(--green);color:#fff;}
.pag-wrap .page-item .page-link:hover{border-color:var(--green);color:var(--green);}
.pag-wrap .page-item.disabled .page-link{opacity:.4;cursor:default;}

@media(max-width:900px){.ann-inner{grid-template-columns:1fr;}.filter-sidebar{position:static;}}
@media(max-width:768px){
    .search-hero{padding:90px 20px 60px;}
    .ann-section{padding:32px 16px;}
    .ann-img{width:120px;}
    .ann-body{padding:14px;}
}
</style>
@endpush

@section('content')

{{-- SEARCH HERO --}}
<div class="search-hero">
    <div class="search-hero-grid"></div>
    <div class="search-hero-glow"></div>
    <div class="search-hero-inner">
        <div class="page-hero-tag" style="margin-bottom:16px;">
            <svg width="8" height="8" viewBox="0 0 8 8"><circle cx="4" cy="4" r="4" fill="#4ade80"/></svg>
            {{ $annonces->total() }} offres disponibles
        </div>
        <h1>Toutes les <span>annonces</span></h1>
        <p>Invendus alimentaires, dons, surplus — trouvez une offre près de chez vous.</p>

        <form action="{{ route('annonces.index') }}" method="GET">
            @foreach(request()->except('ville') as $key => $val)
                <input type="hidden" name="{{ $key }}" value="{{ $val }}">
            @endforeach
            <div class="search-bar">
                <input type="text" name="ville" value="{{ request('ville') }}" placeholder="Rechercher par ville, quartier...">
                <button type="submit"><i class="fas fa-search"></i> Rechercher</button>
            </div>
        </form>

        <div class="stats-pills">
            <div class="stat-pill"><strong>{{ $annonces->total() }}</strong> annonces actives</div>
            <div class="stat-pill"><strong>{{ $categories->count() }}</strong> catégories</div>
            <div class="stat-pill"><i class="fas fa-map-marker-alt" style="color:#4ade80;"></i> Abidjan &amp; environs</div>
        </div>
    </div>
    <svg class="search-hero-wave" viewBox="0 0 1440 56" xmlns="http://www.w3.org/2000/svg">
        <path d="M0,32 C360,56 1080,0 1440,32 L1440,56 L0,56 Z" fill="#f8fafc"/>
    </svg>
</div>

{{-- MAIN --}}
<section class="ann-section">
    <div class="ann-inner">

        {{-- SIDEBAR FILTRES --}}
        <aside class="filter-sidebar">
            <div class="filter-head">
                <i class="fas fa-sliders-h" style="color:rgba(255,255,255,.8);font-size:.85rem;"></i>
                <h5>Filtres</h5>
            </div>
            <div class="filter-body">
                <form action="{{ route('annonces.index') }}" method="GET">

                    <div class="filter-group">
                        <span class="filter-label">Type d'offre</span>
                        @foreach(['vente'=>'Vente','don'=>'Don gratuit','alimentation_animale'=>'Alimentation animale','transformation'=>'Transformation'] as $val=>$lbl)
                        <div class="radio-item">
                            <input type="radio" name="type_offre" value="{{ $val }}" id="t_{{ $val }}"
                                {{ request('type_offre')===$val?'checked':'' }}>
                            <label for="t_{{ $val }}">{{ $lbl }}</label>
                        </div>
                        @endforeach
                        <div class="radio-item">
                            <input type="radio" name="type_offre" value="" id="t_all" {{ !request('type_offre')?'checked':'' }}>
                            <label for="t_all">Toutes les offres</label>
                        </div>
                    </div>

                    <hr class="filter-sep">

                    <div class="filter-group">
                        <span class="filter-label">Catégorie</span>
                        @foreach($categories as $cat)
                        <div class="radio-item">
                            <input type="radio" name="categorie" value="{{ $cat->id }}" id="c_{{ $cat->id }}"
                                {{ request('categorie')==$cat->id?'checked':'' }}>
                            <label for="c_{{ $cat->id }}">{{ $cat->icone }} {{ $cat->nom }}</label>
                        </div>
                        @endforeach
                        <div class="radio-item">
                            <input type="radio" name="categorie" value="" id="c_all" {{ !request('categorie')?'checked':'' }}>
                            <label for="c_all">Toutes les catégories</label>
                        </div>
                    </div>

                    <hr class="filter-sep">

                    <div class="filter-group">
                        <span class="filter-label">Ville / Quartier</span>
                        <input type="text" name="ville" class="filter-text" value="{{ request('ville') }}" placeholder="Ex: Cocody, Yopougon...">
                    </div>

                    <button type="submit" class="btn-filter"><i class="fas fa-search"></i> Appliquer</button>
                    <a href="{{ route('annonces.index') }}" class="btn-reset"><i class="fas fa-times"></i> Réinitialiser</a>
                </form>
            </div>
        </aside>

        {{-- LISTE --}}
        <div class="ann-list">

            {{-- Carte --}}
            <div class="map-toggle-row">
                <span><i class="fas fa-map-marker-alt" style="color:var(--green);margin-right:4px;"></i>Carte des annonces géolocalisées</span>
                <button class="btn-map-toggle" id="mapToggleBtn" onclick="toggleMap()">
                    <i class="fas fa-map"></i> Afficher la carte
                </button>
            </div>
            <div id="mapWrapper" style="display:none;">
                <div id="annoncesMap"></div>
            </div>

            {{-- Barre résultats --}}
            <div class="ann-top-bar">
                <div class="ann-count">
                    <span>{{ $annonces->total() }}</span>
                    annonce{{ $annonces->total()>1?'s':'' }} trouvée{{ $annonces->total()>1?'s':'' }}
                </div>
                <select class="sort-sel" onchange="window.location='?tri='+this.value+'{{ request()->except('tri') ? '&'.http_build_query(request()->except('tri')) : '' }}'">
                    <option value="recent" {{ request('tri','recent')==='recent'?'selected':'' }}>Plus récentes</option>
                    <option value="prix_asc" {{ request('tri')==='prix_asc'?'selected':'' }}>Prix croissant</option>
                    <option value="prix_desc" {{ request('tri')==='prix_desc'?'selected':'' }}>Prix décroissant</option>
                </select>
            </div>

            {{-- Cards --}}
            @forelse($annonces as $annonce)
            @php
                $badges = [
                    'vente'              => ['#2563eb','Vente'],
                    'don'                => ['#16a34a','Don gratuit'],
                    'alimentation_animale'=> ['#d97706','Animale'],
                    'transformation'     => ['#7c3aed','Transfo'],
                ];
                [$bgColor,$badgeLabel] = $badges[$annonce->type_offre] ?? ['#64748b',$annonce->type_offre];
            @endphp
            <div class="ann-card reveal">
                <div class="ann-img">
                    @if($annonce->photoPrincipale)
                        <img src="{{ Storage::url($annonce->photoPrincipale->url) }}" alt="{{ $annonce->titre }}">
                    @else
                        <div class="ann-img-placeholder"><i class="fas fa-image"></i></div>
                    @endif
                    <span class="ann-badge" style="background:{{ $bgColor }};">{{ $badgeLabel }}</span>
                    @if($annonce->estUrgent())
                        <span class="ann-urgent">🔥 {{ $annonce->heuresRestantes() }}h restantes</span>
                    @endif
                </div>
                <div class="ann-body">
                    <div>
                        <span class="ann-cat">{{ $annonce->categorie->icone ?? '' }} {{ $annonce->categorie->nom }}</span>
                        <h3 class="ann-title"><a href="{{ route('annonces.show', $annonce) }}">{{ $annonce->titre }}</a></h3>
                        <p class="ann-desc">{{ Str::limit($annonce->description, 95) }}</p>
                        @if($annonce->type_offre === 'don')
                            <div class="ann-price gratuit">GRATUIT</div>
                        @else
                            <div class="ann-price vente">{{ number_format($annonce->prix,0,',',' ') }} FCFA</div>
                        @endif
                        <div class="ann-meta">
                            <span><i class="fas fa-weight"></i>{{ $annonce->quantite }} {{ $annonce->unite }}</span>
                            <span><i class="fas fa-map-marker-alt"></i>{{ $annonce->adresse_collecte }}</span>
                            <span><i class="fas fa-user"></i>{{ $annonce->user->nom_structure ?? $annonce->user->prenom.' '.$annonce->user->nom }}</span>
                            <span><i class="fas fa-clock"></i>{{ $annonce->created_at->diffForHumans() }}</span>
                            <span><i class="fas fa-eye"></i>{{ $annonce->vues }} vues</span>
                        </div>
                    </div>
                    <div class="ann-actions">
                        <a href="{{ route('annonces.show', $annonce) }}" class="btn-ann-primary">
                            <i class="fas fa-eye"></i> Voir l'annonce
                        </a>
                        @auth
                            @if(Auth::id() !== $annonce->user_id)
                            <a href="{{ route('messages.ouvrir', $annonce->user) }}" class="btn-ann-outline">
                                <i class="fas fa-comment"></i> Contacter
                            </a>
                            @endif
                        @endauth
                    </div>
                </div>
            </div>
            @empty
            <div class="empty-state">
                <i class="fas fa-search"></i>
                <h4>Aucune annonce disponible</h4>
                <p>Essayez d'autres filtres ou revenez plus tard.</p>
                <a href="{{ route('annonces.index') }}" class="btn-ann-primary">Voir toutes les annonces</a>
            </div>
            @endforelse

            {{-- Pagination --}}
            <div class="pag-wrap">
                {{ $annonces->withQueryString()->links('pagination::bootstrap-5') }}
            </div>

        </div>
    </div>
</section>

@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
const annoncesGeo = @json($annoncesGeo);
let map = null, mapInitialized = false;

function toggleMap(){
    const wrapper = document.getElementById('mapWrapper');
    const btn = document.getElementById('mapToggleBtn');
    const visible = wrapper.style.display !== 'none';
    if(visible){
        wrapper.style.display = 'none';
        btn.classList.remove('active');
        btn.innerHTML = '<i class="fas fa-map"></i> Afficher la carte';
    } else {
        wrapper.style.display = 'block';
        btn.classList.add('active');
        btn.innerHTML = '<i class="fas fa-map"></i> Masquer la carte';
        if(!mapInitialized){ initMap(); mapInitialized = true; }
        else { setTimeout(()=>map.invalidateSize(),50); }
    }
}

function initMap(){
    map = L.map('annoncesMap').setView([5.3484,-4.0166],12);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',{attribution:'© OpenStreetMap',maxZoom:19}).addTo(map);
    const colors = {vente:'#2563eb',don:'#16a34a',alimentation_animale:'#d97706',transformation:'#7c3aed'};
    annoncesGeo.forEach(function(a){
        const color = colors[a.type] || '#6b7280';
        const icon = L.divIcon({className:'',
            html:`<div style="background:${color};width:30px;height:30px;border-radius:50% 50% 50% 0;transform:rotate(-45deg);border:3px solid #fff;box-shadow:0 2px 8px rgba(0,0,0,.3);"></div>`,
            iconSize:[30,30],iconAnchor:[15,30],popupAnchor:[0,-32]});
        const photoHtml = a.photo ? `<img src="${a.photo}" style="width:100%;height:72px;object-fit:cover;border-radius:6px;margin-bottom:6px;">` : '';
        const popup = `<div style="min-width:190px;">${photoHtml}<strong style="display:block;margin-bottom:3px;">${a.titre}</strong><span style="color:${color};font-weight:700;">${a.prix}</span>${a.adresse?`<br><span style="color:#888;font-size:.72rem;">📍 ${a.adresse}</span>`:''}<br><a href="${a.url}" style="display:inline-block;margin-top:7px;background:${color};color:#fff;padding:3px 11px;border-radius:20px;font-size:.72rem;">Voir →</a></div>`;
        L.marker([a.lat,a.lng],{icon}).addTo(map).bindPopup(popup);
    });
    const locBtn = L.control({position:'topleft'});
    locBtn.onAdd = function(){
        const div = L.DomUtil.create('div','leaflet-bar');
        div.innerHTML = '<a href="#" title="Ma position" style="font-size:16px;line-height:30px;text-align:center;display:block;width:30px;height:30px;">📍</a>';
        div.onclick = function(e){
            e.preventDefault();
            if(!navigator.geolocation) return;
            navigator.geolocation.getCurrentPosition(function(pos){
                map.setView([pos.coords.latitude,pos.coords.longitude],14);
                L.circleMarker([pos.coords.latitude,pos.coords.longitude],{radius:8,fillColor:'#3b82f6',color:'#fff',weight:2,fillOpacity:1}).addTo(map).bindPopup('Vous êtes ici').openPopup();
            });
        };
        return div;
    };
    locBtn.addTo(map);
    if(annoncesGeo.length > 0){
        const group = L.featureGroup(annoncesGeo.map(a=>L.marker([a.lat,a.lng])));
        map.fitBounds(group.getBounds().pad(0.3));
    }
}
</script>
@endpush
