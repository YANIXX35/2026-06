@extends('layouts.dashboard')
@section('title', 'Ajouter un article')

@section('sidebar-nav')
<span class="nav-section">Navigation</span>
<a href="{{ route('dashboard') }}" class="nav-item">
    <span class="nav-icon"><i class="fas fa-arrow-left"></i></span> Retour au dashboard
</a>
<a href="{{ route('annonces.create') }}" class="nav-item active">
    <span class="nav-icon"><i class="fas fa-plus-circle"></i></span> Nouvel article
</a>
<a href="{{ route('annonces.mes-annonces') }}" class="nav-item">
    <span class="nav-icon"><i class="fas fa-list"></i></span> Mes articles
</a>
<a href="{{ route('annonces.index') }}" class="nav-item">
    <span class="nav-icon"><i class="fas fa-store"></i></span> Voir la boutique
</a>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1>Ajouter un article</h1>
        <p style="font-size:.85rem;color:#888;margin-top:4px;">Publiez un surplus alimentaire — il apparaîtra immédiatement dans la boutique.</p>
    </div>
    <a href="{{ route('dashboard') }}" style="display:flex;align-items:center;gap:8px;font-size:.85rem;color:#888;text-decoration:none;background:#fff;border:1px solid #eee;padding:8px 16px;border-radius:10px;">
        <i class="fas fa-arrow-left"></i> Retour
    </a>
</div>

@if($errors->any())
<div style="background:#fef2f2;border:1px solid #fecaca;color:#dc2626;padding:14px 18px;border-radius:12px;margin-bottom:20px;font-size:.85rem;">
    @foreach($errors->all() as $err)<div>• {{ $err }}</div>@endforeach
</div>
@endif

<form action="{{ route('annonces.store') }}" method="POST" enctype="multipart/form-data" id="createForm">
@csrf

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">

    <!-- Colonne gauche -->
    <div>
        <div class="card">
            <div class="card-header">
                <span class="card-title"><i class="fas fa-info-circle me-2" style="color:#16a34a;"></i>Informations principales</span>
            </div>

            <div style="margin-bottom:16px;">
                <label style="display:block;font-size:.8rem;font-weight:700;color:#555;margin-bottom:6px;">Nom du produit / article *</label>
                <input type="text" name="titre" value="{{ old('titre') }}"
                    placeholder="Ex: Cageots de mangues fraîches Kent"
                    style="width:100%;padding:11px 14px;border:1.5px solid #e5e5e5;border-radius:10px;font-size:.88rem;outline:none;font-family:'Inter',sans-serif;transition:border-color .2s;"
                    onfocus="this.style.borderColor='#16a34a'" onblur="this.style.borderColor='#e5e5e5'" required>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:16px;">
                <div>
                    <label style="display:block;font-size:.8rem;font-weight:700;color:#555;margin-bottom:6px;">Catégorie *</label>
                    <select name="categorie_id" required
                        style="width:100%;padding:11px 14px;border:1.5px solid #e5e5e5;border-radius:10px;font-size:.88rem;outline:none;font-family:'Inter',sans-serif;background:#fff;">
                        <option value="">-- Choisir --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('categorie_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->icone }} {{ $cat->nom }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label style="display:block;font-size:.8rem;font-weight:700;color:#555;margin-bottom:6px;">Type d'offre *</label>
                    <select name="type_offre" id="typeOffre" required
                        style="width:100%;padding:11px 14px;border:1.5px solid #e5e5e5;border-radius:10px;font-size:.88rem;outline:none;font-family:'Inter',sans-serif;background:#fff;">
                        <option value="vente"   {{ old('type_offre')==='vente'?'selected':'' }}>💰 Vente</option>
                        <option value="don"     {{ old('type_offre')==='don'?'selected':'' }}>🎁 Don gratuit</option>
                        <option value="alimentation_animale" {{ old('type_offre')==='alimentation_animale'?'selected':'' }}>🐄 Alimentation animale</option>
                        <option value="transformation" {{ old('type_offre')==='transformation'?'selected':'' }}>🏭 Transformation</option>
                    </select>
                </div>
            </div>

            <div style="margin-bottom:0;">
                <label style="display:block;font-size:.8rem;font-weight:700;color:#555;margin-bottom:6px;">Description</label>
                <textarea name="description" rows="4"
                    placeholder="Décrivez le produit : état, origine, conditions de conservation..."
                    style="width:100%;padding:11px 14px;border:1.5px solid #e5e5e5;border-radius:10px;font-size:.88rem;outline:none;font-family:'Inter',sans-serif;resize:vertical;"
                    onfocus="this.style.borderColor='#16a34a'" onblur="this.style.borderColor='#e5e5e5'">{{ old('description') }}</textarea>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <span class="card-title"><i class="fas fa-map-marker-alt me-2" style="color:#ea580c;"></i>Localisation & Disponibilité</span>
            </div>
            <div style="margin-bottom:16px;">
                <label style="display:block;font-size:.8rem;font-weight:700;color:#555;margin-bottom:6px;">Adresse de collecte *</label>
                <div style="display:flex;gap:8px;">
                    <input type="text" name="adresse_collecte" id="adresse_collecte" value="{{ old('adresse_collecte') }}"
                        placeholder="Ex: Marché de Cocody, Abidjan"
                        style="flex:1;padding:11px 14px;border:1.5px solid #e5e5e5;border-radius:10px;font-size:.88rem;outline:none;font-family:'Inter',sans-serif;"
                        onfocus="this.style.borderColor='#16a34a'" onblur="this.style.borderColor='#e5e5e5'" required>
                    <button type="button" id="btnGeolocate" title="Utiliser ma position actuelle"
                        style="padding:11px 14px;background:#ea580c;color:#fff;border:none;border-radius:10px;cursor:pointer;font-size:.9rem;flex-shrink:0;">
                        <i class="fas fa-crosshairs"></i>
                    </button>
                </div>
            </div>

            {{-- Carte Leaflet --}}
            <div style="margin-bottom:16px;">
                <label style="display:block;font-size:.8rem;font-weight:700;color:#555;margin-bottom:6px;">
                    <i class="fas fa-map me-1" style="color:#3b82f6;"></i>Placer sur la carte <span style="font-weight:400;color:#aaa;">(cliquez pour positionner le marqueur)</span>
                </label>
                <div id="pickMap" style="height:240px;border-radius:12px;border:1.5px solid #e5e5e5;overflow:hidden;z-index:0;"></div>
                <input type="hidden" name="latitude"  id="lat_input"  value="{{ old('latitude') }}">
                <input type="hidden" name="longitude" id="lng_input"  value="{{ old('longitude') }}">
                <p id="coordsDisplay" style="font-size:.72rem;color:#16a34a;margin-top:5px;display:none;">
                    <i class="fas fa-check-circle me-1"></i> Position enregistrée : <span id="coordsText"></span>
                </p>
            </div>

            <div>
                <label style="display:block;font-size:.8rem;font-weight:700;color:#555;margin-bottom:6px;">Date d'expiration</label>
                <input type="datetime-local" name="date_expiration" value="{{ old('date_expiration') }}"
                    style="width:100%;padding:11px 14px;border:1.5px solid #e5e5e5;border-radius:10px;font-size:.88rem;outline:none;font-family:'Inter',sans-serif;">
            </div>
        </div>
    </div>

    <!-- Colonne droite -->
    <div>
        <div class="card">
            <div class="card-header">
                <span class="card-title"><i class="fas fa-weight me-2" style="color:#7c3aed;"></i>Quantité & Prix</span>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:16px;">
                <div>
                    <label style="display:block;font-size:.8rem;font-weight:700;color:#555;margin-bottom:6px;">Quantité *</label>
                    <input type="number" name="quantite" value="{{ old('quantite') }}" step="0.01" min="0.01"
                        placeholder="Ex: 15"
                        style="width:100%;padding:11px 14px;border:1.5px solid #e5e5e5;border-radius:10px;font-size:.88rem;outline:none;font-family:'Inter',sans-serif;"
                        onfocus="this.style.borderColor='#16a34a'" onblur="this.style.borderColor='#e5e5e5'" required>
                </div>
                <div>
                    <label style="display:block;font-size:.8rem;font-weight:700;color:#555;margin-bottom:6px;">Unité *</label>
                    <select name="unite" required
                        style="width:100%;padding:11px 14px;border:1.5px solid #e5e5e5;border-radius:10px;font-size:.88rem;outline:none;font-family:'Inter',sans-serif;background:#fff;">
                        @foreach(['kg'=>'Kilogramme (kg)','g'=>'Gramme (g)','litre'=>'Litre (L)','pièce'=>'Pièce(s)','boîte'=>'Boîte(s)','sac'=>'Sac(s)','lot'=>'Lot(s)','portion'=>'Portion(s)'] as $val => $label)
                            <option value="{{ $val }}" {{ old('unite')===$val?'selected':'' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <!-- Panier Surprise Toggle -->
            <div style="background:#fcf4ff;border:1.5px solid #f0abfc;border-radius:12px;padding:14px;margin-bottom:16px;">
                <label style="display:flex;align-items:center;gap:10px;cursor:pointer;font-weight:700;color:#86198f;font-size:.85rem;">
                    <input type="checkbox" name="est_panier_mystere" value="1" {{ old('est_panier_mystere')?'checked':'' }} style="width:18px;height:18px;accent-color:#c026d3;">
                    🎁 Ceci est un "Panier Surprise / Mystère" (Style Too Good To Go)
                </label>
                <p style="font-size:.73rem;color:#a21caf;margin:4px 0 0 28px;">Proposez un assortiment d'invendus du jour à prix très réduit sans détailler chaque produit.</p>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:16px;">
                <div id="prixWrap">
                    <label style="display:block;font-size:.8rem;font-weight:700;color:#555;margin-bottom:6px;">Prix Vendu (FCFA) *</label>
                    <div style="display:flex;align-items:center;gap:0;border:1.5px solid #e5e5e5;border-radius:10px;overflow:hidden;">
                        <input type="number" name="prix" value="{{ old('prix', 0) }}" min="0" id="prixInput"
                            style="flex:1;padding:11px 14px;border:none;font-size:.88rem;outline:none;font-family:'Inter',sans-serif;">
                        <span style="background:#f9fafb;padding:11px 14px;font-size:.82rem;font-weight:700;color:#888;border-left:1px solid #e5e5e5;">FCFA</span>
                    </div>
                </div>
                <div>
                    <label style="display:block;font-size:.8rem;font-weight:700;color:#555;margin-bottom:6px;">Prix d'origine habituel (FCFA)</label>
                    <div style="display:flex;align-items:center;gap:0;border:1.5px solid #e5e5e5;border-radius:10px;overflow:hidden;">
                        <input type="number" name="prix_original" value="{{ old('prix_original') }}" min="0" placeholder="Ex: 5000"
                            style="flex:1;padding:11px 14px;border:none;font-size:.88rem;outline:none;font-family:'Inter',sans-serif;">
                        <span style="background:#f9fafb;padding:11px 14px;font-size:.82rem;font-weight:700;color:#888;border-left:1px solid #e5e5e5;">FCFA</span>
                    </div>
                    <p style="font-size:.72rem;color:#aaa;margin-top:5px;">Pour afficher le badge de réduction (ex: -60%)</p>
                </div>
            </div>

            <div style="margin-bottom:16px;">
                <label style="display:block;font-size:.8rem;font-weight:700;color:#555;margin-bottom:6px;">Poids estimé en kg (calcul impact CO₂)</label>
                <input type="number" name="poids_estime_kg" value="{{ old('poids_estime_kg', 1.0) }}" step="0.1" min="0.05"
                    style="width:100%;padding:11px 14px;border:1.5px solid #e5e5e5;border-radius:10px;font-size:.88rem;outline:none;font-family:'Inter',sans-serif;">
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <span class="card-title"><i class="fas fa-camera me-2" style="color:#0891b2;"></i>Photo du produit</span>
            </div>
            <div id="dropZone" style="border:2px dashed #e5e5e5;border-radius:14px;padding:32px 20px;text-align:center;cursor:pointer;transition:border-color .2s;background:#fafafa;">
                <div id="previewGrid" style="display:none;flex-wrap:wrap;gap:8px;justify-content:center;margin-bottom:12px;"></div>
                <i class="fas fa-cloud-upload-alt" style="font-size:2rem;color:#bbb;display:block;margin-bottom:10px;" id="uploadIcon"></i>
                <p style="font-size:.82rem;color:#888;margin-bottom:6px;" id="uploadText">Cliquez ou glissez vos photos ici</p>
                <p style="font-size:.72rem;color:#bbb;">JPG, PNG · Max 2 MB · La 1ère sera la photo principale</p>
                <input type="file" name="photos[]" id="photosInput" multiple accept="image/*" style="display:none;">
            </div>
            <p style="font-size:.75rem;color:#16a34a;margin-top:8px;font-weight:600;"><i class="fas fa-lightbulb me-1"></i>Une belle photo augmente les chances d'échange !</p>
        </div>

        <!-- Boutons -->
        <div style="display:flex;gap:12px;">
            <button type="submit"
                style="flex:1;background:#16a34a;color:#fff;border:none;padding:15px;border-radius:12px;font-size:.95rem;font-weight:700;cursor:pointer;font-family:'Inter',sans-serif;transition:background .2s;display:flex;align-items:center;justify-content:center;gap:8px;"
                onmouseover="this.style.background='#15803d'" onmouseout="this.style.background='#16a34a'">
                <i class="fas fa-paper-plane"></i> Publier l'article
            </button>
            <a href="{{ route('dashboard') }}"
                style="padding:15px 24px;border:1.5px solid #e5e5e5;border-radius:12px;font-size:.88rem;font-weight:600;color:#666;text-decoration:none;display:flex;align-items:center;gap:6px;transition:border-color .2s;"
                onmouseover="this.style.borderColor='#aaa'" onmouseout="this.style.borderColor='#e5e5e5'">
                <i class="fas fa-times"></i> Annuler
            </a>
        </div>
    </div>
</div>
</form>
@endsection

@section('right-panel')
<div class="card" style="margin-bottom:20px;">
    <div class="card-title" style="margin-bottom:14px;"><i class="fas fa-lightbulb me-2" style="color:#f59e0b;"></i>Conseils</div>
    @foreach([
        ['🖼️','Ajoutez 2-3 photos claires sous bonne lumière'],
        ['📝','Décrivez précisément l\'état du produit'],
        ['📍','Indiquez une adresse précise pour la collecte'],
        ['💰','Prix adapté = plus de demandes rapidement'],
        ['📅','Mettez une date d\'expiration réaliste'],
    ] as $c)
    <div style="display:flex;align-items:flex-start;gap:10px;padding:9px 0;border-bottom:1px solid #f5f5f5;font-size:.8rem;color:#555;line-height:1.5;">
        <span style="font-size:1rem;">{{ $c[0] }}</span>{{ $c[1] }}
    </div>
    @endforeach
</div>

<div class="card">
    <div class="card-title" style="margin-bottom:12px;"><i class="fas fa-info-circle me-2" style="color:#3b82f6;"></i>Votre article apparaîtra sur</div>
    <div style="background:#f0fdf4;border-radius:10px;padding:14px;margin-bottom:10px;">
        <div style="font-size:.78rem;font-weight:700;color:#16a34a;margin-bottom:4px;">🏠 Page d'accueil</div>
        <div style="font-size:.72rem;color:#888;">Section "Boutique" en temps réel</div>
    </div>
    <div style="background:#f0f9ff;border-radius:10px;padding:14px;">
        <div style="font-size:.78rem;font-weight:700;color:#0369a1;margin-bottom:4px;">🔍 Page Annonces</div>
        <div style="font-size:.72rem;color:#888;">Visible par tous les acheteurs</div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
// ── CARTE LEAFLET (sélecteur de position) ─────────────────────────────────
(function(){
    // Centre par défaut : Abidjan
    const defaultLat = {{ old('latitude', 5.3484) }};
    const defaultLng = {{ old('longitude', -4.0166) }};

    const map = L.map('pickMap').setView([defaultLat, defaultLng], 12);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap',
        maxZoom: 19
    }).addTo(map);

    let marker = null;

    // Si des coordonnées existantes (old())
    if ({{ old('latitude') ? 'true' : 'false' }}) {
        marker = L.marker([defaultLat, defaultLng], {draggable: true}).addTo(map);
        setCoords(defaultLat, defaultLng);
        marker.on('dragend', () => setCoords(marker.getLatLng().lat, marker.getLatLng().lng));
    }

    // Clic sur la carte → place/déplace le marqueur
    map.on('click', function(e){
        const { lat, lng } = e.latlng;
        if (marker) {
            marker.setLatLng([lat, lng]);
        } else {
            marker = L.marker([lat, lng], {draggable: true}).addTo(map);
            marker.on('dragend', () => setCoords(marker.getLatLng().lat, marker.getLatLng().lng));
        }
        setCoords(lat, lng);
    });

    function setCoords(lat, lng){
        document.getElementById('lat_input').value  = lat.toFixed(7);
        document.getElementById('lng_input').value  = lng.toFixed(7);
        document.getElementById('coordsText').textContent = lat.toFixed(5) + ', ' + lng.toFixed(5);
        document.getElementById('coordsDisplay').style.display = 'block';
    }

    // Bouton géolocalisation GPS
    document.getElementById('btnGeolocate').addEventListener('click', function(){
        if (!navigator.geolocation) { alert("Géolocalisation non supportée par votre navigateur."); return; }
        this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        const btn = this;
        navigator.geolocation.getCurrentPosition(function(pos){
            const lat = pos.coords.latitude;
            const lng = pos.coords.longitude;
            map.setView([lat, lng], 16);
            if (marker) { marker.setLatLng([lat, lng]); }
            else {
                marker = L.marker([lat, lng], {draggable: true}).addTo(map);
                marker.on('dragend', () => setCoords(marker.getLatLng().lat, marker.getLatLng().lng));
            }
            setCoords(lat, lng);
            btn.innerHTML = '<i class="fas fa-crosshairs"></i>';
        }, function(){
            alert("Impossible de récupérer votre position.");
            btn.innerHTML = '<i class="fas fa-crosshairs"></i>';
        });
    });
})();
</script>
<script>
// Type offre → cache prix si don
document.getElementById('typeOffre').addEventListener('change', function(){
    const pw = document.getElementById('prixWrap');
    const pi = document.getElementById('prixInput');
    if(this.value === 'don'){ pw.style.opacity='.4'; pi.value=0; pi.disabled=true; }
    else { pw.style.opacity='1'; pi.disabled=false; }
});

// Drag & drop photos + preview
const drop = document.getElementById('dropZone');
const inp  = document.getElementById('photosInput');
const grid = document.getElementById('previewGrid');
const icon = document.getElementById('uploadIcon');
const txt  = document.getElementById('uploadText');

drop.addEventListener('click', () => inp.click());
drop.addEventListener('dragover', e => { e.preventDefault(); drop.style.borderColor='#16a34a'; drop.style.background='#f0fdf4'; });
drop.addEventListener('dragleave', () => { drop.style.borderColor='#e5e5e5'; drop.style.background='#fafafa'; });
drop.addEventListener('drop', e => { e.preventDefault(); drop.style.borderColor='#e5e5e5'; drop.style.background='#fafafa'; showPreviews(e.dataTransfer.files); });
inp.addEventListener('change', () => showPreviews(inp.files));

function showPreviews(files){
    grid.innerHTML=''; grid.style.display='flex';
    icon.style.display='none'; txt.style.display='none';
    Array.from(files).forEach((f,i) => {
        const r = new FileReader();
        r.onload = e => {
            const d = document.createElement('div');
            d.style.cssText='position:relative;width:70px;height:70px;';
            d.innerHTML=`<img src="${e.target.result}" style="width:70px;height:70px;object-fit:cover;border-radius:10px;border:2px solid ${i===0?'#16a34a':'#eee'};">
                ${i===0?'<span style="position:absolute;bottom:3px;left:50%;transform:translateX(-50%);background:#16a34a;color:#fff;font-size:.55rem;font-weight:700;padding:1px 6px;border-radius:4px;white-space:nowrap;">Principal</span>':''}`;
            grid.appendChild(d);
        };
        r.readAsDataURL(f);
    });
    // DataTransfer pour conserver les fichiers
    const dt = new DataTransfer();
    Array.from(files).forEach(f => dt.items.add(f));
    inp.files = dt.files;
}
</script>
@endpush
