@extends('layouts.app')
@section('title', 'Toutes les annonces')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<style>
#mapToggleBtn.active { background:#16a34a; color:#fff; border-color:#16a34a; }
#annoncesMap { height:420px; border-radius:14px; overflow:hidden; }
.leaflet-popup-content { font-size:.82rem; line-height:1.5; min-width:180px; }
.leaflet-popup-content a { color:#16a34a; font-weight:600; text-decoration:none; }
</style>
@endpush

@section('content')
<div class="container-fluid bg-light py-3 border-bottom">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="/" class="text-primary">Accueil</a></li>
                <li class="breadcrumb-item active">Annonces</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container-fluid py-5">
    <div class="container">
        <div class="row g-4">

            <!-- Filtres sidebar -->
            <div class="col-lg-3">
                <div class="card border-0 shadow-sm rounded-3 sticky-top" style="top:10px;">
                    <div class="card-header bg-primary text-white rounded-top-3">
                        <h6 class="mb-0"><i class="fas fa-filter me-2"></i>Filtres</h6>
                    </div>
                    <div class="card-body p-4">
                        <form action="{{ route('annonces.index') }}" method="GET">

                            <div class="mb-3">
                                <label class="form-label fw-semibold small text-muted text-uppercase">Type d'offre</label>
                                @foreach(['vente' => '💰 Vente', 'don' => '🎁 Don gratuit', 'alimentation_animale' => '🐄 Alimentation animale', 'transformation' => '🏭 Transformation'] as $val => $label)
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="type_offre" value="{{ $val }}" id="t_{{ $val }}" {{ request('type_offre') === $val ? 'checked' : '' }}>
                                    <label class="form-check-label" for="t_{{ $val }}">{{ $label }}</label>
                                </div>
                                @endforeach
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="type_offre" value="" id="t_all" {{ !request('type_offre') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="t_all">Toutes</label>
                                </div>
                            </div>

                            <hr>
                            <div class="mb-3">
                                <label class="form-label fw-semibold small text-muted text-uppercase">Catégorie</label>
                                @foreach($categories as $cat)
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="categorie" value="{{ $cat->id }}" id="c_{{ $cat->id }}" {{ request('categorie') == $cat->id ? 'checked' : '' }}>
                                    <label class="form-check-label" for="c_{{ $cat->id }}">{{ $cat->icone }} {{ $cat->nom }}</label>
                                </div>
                                @endforeach
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="categorie" value="" id="c_all" {{ !request('categorie') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="c_all">Toutes</label>
                                </div>
                            </div>

                            <hr>
                            <div class="mb-3">
                                <label class="form-label fw-semibold small text-muted text-uppercase">Ville / Quartier</label>
                                <input type="text" name="ville" class="form-control form-control-sm" value="{{ request('ville') }}" placeholder="Ex: Cocody, Yopougon...">
                            </div>

                            <button type="submit" class="btn btn-primary w-100 rounded-pill">
                                <i class="fas fa-search me-2"></i>Appliquer
                            </button>
                            <a href="{{ route('annonces.index') }}" class="btn btn-outline-secondary w-100 rounded-pill mt-2">
                                <i class="fas fa-times me-2"></i>Réinitialiser
                            </a>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Liste annonces -->
            <div class="col-lg-9">

                {{-- Carte interactive --}}
                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted small"><i class="fas fa-map-marker-alt me-1 text-primary"></i>Carte des annonces disponibles</span>
                        <button id="mapToggleBtn" class="btn btn-outline-primary btn-sm rounded-pill px-3" onclick="toggleMap()">
                            <i class="fas fa-map me-1"></i> Afficher la carte
                        </button>
                    </div>
                    <div id="mapWrapper" style="display:none;">
                        <div id="annoncesMap"></div>
                        <p class="text-muted small mt-1"><i class="fas fa-info-circle me-1"></i>Seules les annonces avec une position géolocalisée apparaissent sur la carte.</p>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="mb-0">
                        <span class="badge bg-primary rounded-pill me-2">{{ $annonces->total() }}</span>
                        annonce(s) disponible(s)
                    </h5>
                    <select class="form-select form-select-sm w-auto" onchange="window.location='?tri='+this.value">
                        <option value="recent">Plus récentes</option>
                        <option value="prix_asc">Prix croissant</option>
                        <option value="prix_desc">Prix décroissant</option>
                    </select>
                </div>

                @forelse($annonces as $annonce)
                <div class="card border-0 shadow-sm rounded-3 mb-4 wow fadeInUp" data-wow-delay="0.1s">
                    <div class="row g-0">
                        <div class="col-md-3">
                            <div class="position-relative h-100">
                                @if($annonce->photoPrincipale)
                                    <img src="{{ Storage::url($annonce->photoPrincipale->url) }}" class="img-fluid h-100 rounded-start" style="object-fit:cover;width:100%;" alt="{{ $annonce->titre }}">
                                @else
                                    <div class="bg-light d-flex align-items-center justify-content-center h-100 rounded-start" style="min-height:150px;">
                                        <i class="fas fa-image fa-3x text-muted"></i>
                                    </div>
                                @endif
                                @php
                                    $badges = ['vente'=>['bg-primary','💰 Vente'], 'don'=>['bg-success','🎁 Don'], 'alimentation_animale'=>['bg-warning','🐄 Animale'], 'transformation'=>['bg-purple','🏭 Transfo']];
                                    [$bg, $label] = $badges[$annonce->type_offre] ?? ['bg-secondary', $annonce->type_offre];
                                @endphp
                                <span class="badge {{ $bg }} position-absolute top-0 start-0 m-2">{{ $label }}</span>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <span class="badge bg-light text-dark border mb-2">{{ $annonce->categorie->icone ?? '' }} {{ $annonce->categorie->nom }}</span>
                                        <h5 class="card-title mb-1">
                                            <a href="{{ route('annonces.show', $annonce) }}" class="text-dark text-decoration-none">{{ $annonce->titre }}</a>
                                        </h5>
                                        <p class="text-muted small mb-2">{{ Str::limit($annonce->description, 100) }}</p>
                                    </div>
                                    <div class="text-end ms-3">
                                        @if($annonce->type_offre === 'don')
                                            <span class="h5 text-success fw-bold">GRATUIT</span>
                                        @else
                                            <span class="h5 text-primary fw-bold">{{ number_format($annonce->prix, 0, ',', ' ') }} FCFA</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="d-flex flex-wrap gap-3 text-muted small mt-2">
                                    <span><i class="fas fa-weight me-1 text-primary"></i>{{ $annonce->quantite }} {{ $annonce->unite }}</span>
                                    <span><i class="fas fa-map-marker-alt me-1 text-primary"></i>{{ $annonce->adresse_collecte }}</span>
                                    <span><i class="fas fa-user me-1 text-primary"></i>{{ $annonce->user->nom_structure ?? $annonce->user->prenom.' '.$annonce->user->nom }}</span>
                                    <span><i class="fas fa-clock me-1 text-primary"></i>{{ $annonce->created_at->diffForHumans() }}</span>
                                    <span><i class="fas fa-eye me-1 text-primary"></i>{{ $annonce->vues }} vues</span>
                                </div>
                                <div class="mt-3">
                                    <a href="{{ route('annonces.show', $annonce) }}" class="btn btn-primary btn-sm rounded-pill px-4 me-2">
                                        <i class="fas fa-eye me-1"></i>Voir l'annonce
                                    </a>
                                    @auth
                                        @if(Auth::id() !== $annonce->user_id)
                                        <a href="{{ route('messages.ouvrir', $annonce->user) }}" class="btn btn-outline-primary btn-sm rounded-pill px-4">
                                            <i class="fas fa-comment me-1"></i>Contacter
                                        </a>
                                        @endif
                                    @endauth
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-5">
                    <i class="fas fa-search fa-4x text-muted mb-4"></i>
                    <h4 class="text-muted">Aucune annonce disponible</h4>
                    <p class="text-muted">Essayez d'autres filtres ou revenez plus tard.</p>
                    <a href="{{ route('annonces.index') }}" class="btn btn-primary rounded-pill px-5">Voir toutes les annonces</a>
                </div>
                @endforelse

                <div class="d-flex justify-content-center mt-4">
                    {{ $annonces->withQueryString()->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
// Données des annonces géolocalisées
const annoncesGeo = @json(
    $annonces->filter(fn($a) => $a->latitude && $a->longitude)->map(fn($a) => [
        'lat'     => (float) $a->latitude,
        'lng'     => (float) $a->longitude,
        'titre'   => $a->titre,
        'prix'    => $a->type_offre === 'don' ? 'GRATUIT' : number_format($a->prix, 0, ',', ' ').' FCFA',
        'adresse' => $a->adresse_collecte,
        'type'    => $a->type_offre,
        'url'     => route('annonces.show', $a),
        'photo'   => $a->photoPrincipale ? asset('storage/'.$a->photoPrincipale->url) : null,
    ])->values()
);

let map = null;
let mapInitialized = false;

function toggleMap(){
    const wrapper = document.getElementById('mapWrapper');
    const btn     = document.getElementById('mapToggleBtn');
    const visible = wrapper.style.display !== 'none';

    if (visible) {
        wrapper.style.display = 'none';
        btn.classList.remove('active');
        btn.innerHTML = '<i class="fas fa-map me-1"></i> Afficher la carte';
    } else {
        wrapper.style.display = 'block';
        btn.classList.add('active');
        btn.innerHTML = '<i class="fas fa-map me-1"></i> Masquer la carte';
        if (!mapInitialized) { initMap(); mapInitialized = true; }
    }
}

function initMap(){
    map = L.map('annoncesMap').setView([5.3484, -4.0166], 12);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap', maxZoom: 19
    }).addTo(map);

    const colors = { vente:'#2563eb', don:'#16a34a', alimentation_animale:'#d97706', transformation:'#7c3aed' };

    annoncesGeo.forEach(function(a){
        const color = colors[a.type] || '#6b7280';

        const icon = L.divIcon({
            className: '',
            html: `<div style="background:${color};width:32px;height:32px;border-radius:50% 50% 50% 0;transform:rotate(-45deg);border:3px solid #fff;box-shadow:0 2px 8px rgba(0,0,0,.3);"></div>`,
            iconSize: [32, 32],
            iconAnchor: [16, 32],
            popupAnchor: [0, -34],
        });

        const photoHtml = a.photo
            ? `<img src="${a.photo}" style="width:100%;height:80px;object-fit:cover;border-radius:6px;margin-bottom:8px;">`
            : '';

        const popup = `
            <div style="min-width:200px;">
                ${photoHtml}
                <strong style="display:block;margin-bottom:4px;">${a.titre}</strong>
                <span style="color:${color};font-weight:700;font-size:1rem;">${a.prix}</span>
                ${a.adresse ? `<br><span style="color:#888;font-size:.75rem;"><i>📍 ${a.adresse}</i></span>` : ''}
                <br><a href="${a.url}" style="display:inline-block;margin-top:8px;background:${color};color:#fff;padding:4px 12px;border-radius:20px;font-size:.75rem;">Voir l'annonce →</a>
            </div>`;

        L.marker([a.lat, a.lng], {icon}).addTo(map).bindPopup(popup);
    });

    // Bouton "Ma position"
    const locBtn = L.control({position: 'topleft'});
    locBtn.onAdd = function(){
        const div = L.DomUtil.create('div', 'leaflet-bar');
        div.innerHTML = '<a href="#" title="Ma position" style="font-size:18px;line-height:30px;text-align:center;display:block;width:30px;height:30px;">📍</a>';
        div.onclick = function(e){
            e.preventDefault();
            if (!navigator.geolocation) return;
            navigator.geolocation.getCurrentPosition(function(pos){
                map.setView([pos.coords.latitude, pos.coords.longitude], 14);
                L.circleMarker([pos.coords.latitude, pos.coords.longitude], {
                    radius: 8, fillColor: '#3b82f6', color: '#fff', weight: 2, fillOpacity: 1
                }).addTo(map).bindPopup('Vous êtes ici').openPopup();
            });
        };
        return div;
    };
    locBtn.addTo(map);

    if (annoncesGeo.length > 0) {
        const group = L.featureGroup(annoncesGeo.map(a => L.marker([a.lat, a.lng])));
        map.fitBounds(group.getBounds().pad(0.3));
    }
}
</script>
@endpush
