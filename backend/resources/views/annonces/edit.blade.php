@extends('layouts.dashboard')
@section('title', 'Modifier l\'article')

@section('sidebar-nav')
<span class="nav-section">Navigation</span>
<a href="{{ route('dashboard') }}" class="nav-item">
    <span class="nav-icon"><i class="fas fa-arrow-left"></i></span> Retour au dashboard
</a>
<a href="{{ route('annonces.create') }}" class="nav-item">
    <span class="nav-icon"><i class="fas fa-plus-circle"></i></span> Nouvel article
</a>
<a href="{{ route('annonces.mes-annonces') }}" class="nav-item">
    <span class="nav-icon"><i class="fas fa-list"></i></span> Mes articles
</a>
<span class="nav-section">Article actuel</span>
<a href="#" class="nav-item active">
    <span class="nav-icon"><i class="fas fa-edit"></i></span> Modifier
</a>
<a href="{{ route('annonces.show', $annonce) }}" class="nav-item">
    <span class="nav-icon"><i class="fas fa-eye"></i></span> Voir l'article
</a>
<span class="nav-section">Boutique</span>
<a href="{{ route('annonces.index') }}" class="nav-item">
    <span class="nav-icon"><i class="fas fa-store"></i></span> Voir la boutique
</a>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1><i class="fas fa-edit" style="color:#16a34a;font-size:1.2rem;margin-right:8px;"></i>Modifier l'article</h1>
        <p style="font-size:.85rem;color:#888;margin-top:4px;">Mettez à jour les informations de votre article.</p>
    </div>
    <a href="{{ route('annonces.mes-annonces') }}" style="display:flex;align-items:center;gap:6px;font-size:.82rem;color:#888;text-decoration:none;background:#f5f5f5;padding:8px 16px;border-radius:50px;">
        <i class="fas fa-arrow-left"></i> Retour à mes articles
    </a>
</div>

@if($errors->any())
<div style="background:#fef2f2;border:1px solid #fecaca;border-radius:10px;padding:14px 18px;margin-bottom:20px;">
    <div style="font-weight:600;color:#dc2626;font-size:.85rem;margin-bottom:6px;"><i class="fas fa-exclamation-circle me-2"></i>Erreurs de validation :</div>
    <ul style="margin:0;padding-left:18px;color:#dc2626;font-size:.82rem;">
        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
    </ul>
</div>
@endif

<form action="{{ route('annonces.update', $annonce) }}" method="POST" enctype="multipart/form-data" id="editForm">
    @csrf @method('PUT')
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">

        <!-- Colonne gauche -->
        <div>
            <div class="card" style="margin-bottom:20px;">
                <div class="card-header">
                    <span class="card-title"><i class="fas fa-tag me-2" style="color:#16a34a;"></i>Informations du produit</span>
                </div>
                <div style="padding:20px;display:flex;flex-direction:column;gap:16px;">
                    <div>
                        <label style="font-size:.78rem;font-weight:600;color:#444;display:block;margin-bottom:6px;">Nom du produit <span style="color:#ef4444;">*</span></label>
                        <input type="text" name="titre" value="{{ old('titre', $annonce->titre) }}" required
                            style="width:100%;padding:10px 14px;border:1.5px solid #e5e7eb;border-radius:8px;font-size:.85rem;outline:none;box-sizing:border-box;font-family:inherit;"
                            placeholder="Ex: Tomates fraîches surplus marché"
                            onfocus="this.style.borderColor='#16a34a'" onblur="this.style.borderColor='#e5e7eb'">
                    </div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                        <div>
                            <label style="font-size:.78rem;font-weight:600;color:#444;display:block;margin-bottom:6px;">Catégorie <span style="color:#ef4444;">*</span></label>
                            <select name="categorie_id" required
                                style="width:100%;padding:10px 14px;border:1.5px solid #e5e7eb;border-radius:8px;font-size:.85rem;outline:none;background:#fff;box-sizing:border-box;font-family:inherit;">
                                <option value="">-- Choisir --</option>
                                @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('categorie_id', $annonce->categorie_id) == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->icone ?? '' }} {{ $cat->nom }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label style="font-size:.78rem;font-weight:600;color:#444;display:block;margin-bottom:6px;">Type d'offre <span style="color:#ef4444;">*</span></label>
                            <select name="type_offre" id="typeOffre" required
                                style="width:100%;padding:10px 14px;border:1.5px solid #e5e7eb;border-radius:8px;font-size:.85rem;outline:none;background:#fff;box-sizing:border-box;font-family:inherit;">
                                @foreach(['vente'=>'🛒 Vente','don'=>'🎁 Don gratuit','alimentation_animale'=>'🐄 Alimentation animale','transformation'=>'🏭 Pour transformation'] as $val => $label)
                                <option value="{{ $val }}" {{ old('type_offre', $annonce->type_offre) === $val ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div>
                        <label style="font-size:.78rem;font-weight:600;color:#444;display:block;margin-bottom:6px;">Description</label>
                        <textarea name="description" rows="4"
                            style="width:100%;padding:10px 14px;border:1.5px solid #e5e7eb;border-radius:8px;font-size:.85rem;outline:none;resize:vertical;font-family:inherit;box-sizing:border-box;"
                            placeholder="Décrivez votre produit, état, conditions..."
                            onfocus="this.style.borderColor='#16a34a'" onblur="this.style.borderColor='#e5e7eb'">{{ old('description', $annonce->description) }}</textarea>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <span class="card-title"><i class="fas fa-map-marker-alt me-2" style="color:#16a34a;"></i>Localisation & Disponibilité</span>
                </div>
                <div style="padding:20px;display:flex;flex-direction:column;gap:16px;">
                    <div>
                        <label style="font-size:.78rem;font-weight:600;color:#444;display:block;margin-bottom:6px;">Adresse de collecte</label>
                        <div style="display:flex;gap:8px;">
                            <input type="text" name="adresse_collecte" id="adresse_collecte" value="{{ old('adresse_collecte', $annonce->adresse_collecte) }}"
                                style="flex:1;padding:10px 14px;border:1.5px solid #e5e7eb;border-radius:8px;font-size:.85rem;outline:none;font-family:inherit;"
                                placeholder="Ex: Marché de Cocody, Abidjan"
                                onfocus="this.style.borderColor='#16a34a'" onblur="this.style.borderColor='#e5e7eb'">
                            <button type="button" id="btnGeolocate" title="Utiliser ma position actuelle"
                                style="padding:10px 14px;background:#ea580c;color:#fff;border:none;border-radius:8px;cursor:pointer;font-size:.9rem;flex-shrink:0;">
                                <i class="fas fa-crosshairs"></i>
                            </button>
                        </div>
                    </div>
                    {{-- Carte --}}
                    <div>
                        <label style="font-size:.78rem;font-weight:600;color:#444;display:block;margin-bottom:6px;">
                            <i class="fas fa-map me-1" style="color:#3b82f6;"></i>Position sur la carte <span style="font-weight:400;color:#aaa;">(cliquez pour déplacer)</span>
                        </label>
                        <div id="pickMap" style="height:220px;border-radius:10px;border:1.5px solid #e5e7eb;overflow:hidden;z-index:0;"></div>
                        <input type="hidden" name="latitude"  id="lat_input"  value="{{ old('latitude', $annonce->latitude) }}">
                        <input type="hidden" name="longitude" id="lng_input"  value="{{ old('longitude', $annonce->longitude) }}">
                        <p id="coordsDisplay" style="font-size:.72rem;color:#16a34a;margin-top:5px;{{ ($annonce->latitude) ? '' : 'display:none' }}">
                            <i class="fas fa-check-circle me-1"></i> Position : <span id="coordsText">{{ $annonce->latitude ? $annonce->latitude.', '.$annonce->longitude : '' }}</span>
                        </p>
                    </div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                        <div>
                            <label style="font-size:.78rem;font-weight:600;color:#444;display:block;margin-bottom:6px;">Date d'expiration</label>
                            <input type="date" name="date_expiration" value="{{ old('date_expiration', $annonce->date_expiration?->format('Y-m-d')) }}"
                                style="width:100%;padding:10px 14px;border:1.5px solid #e5e7eb;border-radius:8px;font-size:.85rem;outline:none;box-sizing:border-box;font-family:inherit;"
                                onfocus="this.style.borderColor='#16a34a'" onblur="this.style.borderColor='#e5e7eb'">
                        </div>
                        <div>
                            <label style="font-size:.78rem;font-weight:600;color:#444;display:block;margin-bottom:6px;">Statut</label>
                            <select name="statut"
                                style="width:100%;padding:10px 14px;border:1.5px solid #e5e7eb;border-radius:8px;font-size:.85rem;outline:none;background:#fff;box-sizing:border-box;font-family:inherit;">
                                <option value="disponible" {{ old('statut', $annonce->statut) === 'disponible' ? 'selected' : '' }}>✅ Disponible</option>
                                <option value="reservé" {{ old('statut', $annonce->statut) === 'reservé' ? 'selected' : '' }}>🔒 Réservé</option>
                                <option value="expiré" {{ old('statut', $annonce->statut) === 'expiré' ? 'selected' : '' }}>⌛ Expiré</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Colonne droite -->
        <div>
            <div class="card" style="margin-bottom:20px;">
                <div class="card-header">
                    <span class="card-title"><i class="fas fa-balance-scale me-2" style="color:#16a34a;"></i>Quantité & Prix</span>
                </div>
                <div style="padding:20px;display:flex;flex-direction:column;gap:16px;">
                    <div style="display:grid;grid-template-columns:1.5fr 1fr;gap:12px;">
                        <div>
                            <label style="font-size:.78rem;font-weight:600;color:#444;display:block;margin-bottom:6px;">Quantité disponible <span style="color:#ef4444;">*</span></label>
                            <input type="number" name="quantite" value="{{ old('quantite', $annonce->quantite) }}" min="0.1" step="0.1" required
                                style="width:100%;padding:10px 14px;border:1.5px solid #e5e7eb;border-radius:8px;font-size:.85rem;outline:none;box-sizing:border-box;font-family:inherit;"
                                placeholder="Ex: 50"
                                onfocus="this.style.borderColor='#16a34a'" onblur="this.style.borderColor='#e5e7eb'">
                        </div>
                        <div>
                            <label style="font-size:.78rem;font-weight:600;color:#444;display:block;margin-bottom:6px;">Unité <span style="color:#ef4444;">*</span></label>
                            <select name="unite" required
                                style="width:100%;padding:10px 14px;border:1.5px solid #e5e7eb;border-radius:8px;font-size:.85rem;outline:none;background:#fff;box-sizing:border-box;font-family:inherit;">
                                @foreach(['kg','g','L','mL','unité','boîte','sac','caisse'] as $u)
                                <option value="{{ $u }}" {{ old('unite', $annonce->unite) === $u ? 'selected' : '' }}>{{ $u }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div id="prixField" style="{{ old('type_offre', $annonce->type_offre) === 'don' ? 'display:none' : '' }}">
                        <label style="font-size:.78rem;font-weight:600;color:#444;display:block;margin-bottom:6px;">Prix unitaire</label>
                        <div style="position:relative;">
                            <input type="number" name="prix" value="{{ old('prix', $annonce->prix) }}" min="0"
                                style="width:100%;padding:10px 14px 10px 14px;padding-right:70px;border:1.5px solid #e5e7eb;border-radius:8px;font-size:.85rem;outline:none;box-sizing:border-box;font-family:inherit;"
                                placeholder="0"
                                onfocus="this.style.borderColor='#16a34a'" onblur="this.style.borderColor='#e5e7eb'">
                            <span id="prixUnit" style="position:absolute;right:14px;top:50%;transform:translateY(-50%);font-size:.75rem;color:#888;font-weight:600;">FCFA</span>
                        </div>
                        <div style="font-size:.7rem;color:#bbb;margin-top:4px;">Laissez 0 si le prix est à discuter</div>
                    </div>
                    <div id="donNote" style="display:{{ old('type_offre', $annonce->type_offre) === 'don' ? 'block' : 'none' }};background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;padding:10px 14px;">
                        <div style="font-size:.78rem;color:#15803d;font-weight:600;"><i class="fas fa-gift me-2"></i>Don gratuit — aucun prix requis</div>
                        <div style="font-size:.7rem;color:#4ade80;margin-top:2px;">Votre générosité contribue à réduire le gaspillage !</div>
                    </div>
                </div>
            </div>

            <!-- Photos actuelles -->
            @if($annonce->photos->count())
            <div class="card" style="margin-bottom:20px;">
                <div class="card-header">
                    <span class="card-title"><i class="fas fa-images me-2" style="color:#16a34a;"></i>Photos actuelles</span>
                    <span style="font-size:.72rem;color:#bbb;">{{ $annonce->photos->count() }} photo(s)</span>
                </div>
                <div style="padding:16px;display:flex;gap:10px;flex-wrap:wrap;">
                    @foreach($annonce->photos as $i => $photo)
                    <div style="position:relative;">
                        <img src="{{ $photo->url }}" alt=""
                            style="width:80px;height:80px;object-fit:cover;border-radius:8px;border:2px solid {{ $i===0?'#16a34a':'#e5e7eb' }};"
                            onerror="this.parentElement.style.display='none'">
                        @if($i === 0)
                        <span style="position:absolute;top:4px;left:4px;background:#16a34a;color:#fff;font-size:.55rem;font-weight:700;padding:2px 5px;border-radius:4px;">Principal</span>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Ajouter de nouvelles photos -->
            <div class="card">
                <div class="card-header">
                    <span class="card-title"><i class="fas fa-camera me-2" style="color:#16a34a;"></i>Remplacer / Ajouter des photos</span>
                </div>
                <div style="padding:20px;">
                    <div id="dropZone"
                        style="border:2px dashed #d1fae5;border-radius:12px;padding:32px 20px;text-align:center;cursor:pointer;transition:all .2s;background:#fafffe;"
                        onclick="document.getElementById('photoInput').click()"
                        ondragover="event.preventDefault();this.style.borderColor='#16a34a';this.style.background='#f0fdf4'"
                        ondragleave="this.style.borderColor='#d1fae5';this.style.background='#fafffe'"
                        ondrop="handleDrop(event)">
                        <div style="font-size:2rem;margin-bottom:8px;">📷</div>
                        <div style="font-size:.82rem;font-weight:600;color:#16a34a;margin-bottom:4px;">Glisser des photos ici</div>
                        <div style="font-size:.72rem;color:#bbb;">ou cliquer pour sélectionner · JPG, PNG, WebP · max 2Mo</div>
                        <div style="font-size:.7rem;color:#aaa;margin-top:6px;">Les nouvelles photos remplaceront les anciennes</div>
                    </div>
                    <input type="file" id="photoInput" name="photos[]" multiple accept="image/*" style="display:none;" onchange="previewPhotos(this.files)">
                    <div id="previewGrid" style="display:none;margin-top:14px;display:grid;grid-template-columns:repeat(4,1fr);gap:8px;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div style="margin-top:20px;display:flex;gap:12px;align-items:center;">
        <button type="submit"
            style="background:#16a34a;color:#fff;border:none;padding:12px 32px;border-radius:50px;font-size:.88rem;font-weight:600;cursor:pointer;display:flex;align-items:center;gap:8px;font-family:inherit;transition:background .2s;"
            onmouseover="this.style.background='#15803d'" onmouseout="this.style.background='#16a34a'">
            <i class="fas fa-save"></i> Enregistrer les modifications
        </button>
        <a href="{{ route('annonces.show', $annonce) }}"
            style="background:#f5f5f5;color:#444;padding:12px 28px;border-radius:50px;text-decoration:none;font-size:.85rem;font-weight:500;">
            <i class="fas fa-eye me-2"></i>Voir l'article
        </a>
        <a href="{{ route('annonces.mes-annonces') }}"
            style="color:#888;text-decoration:none;font-size:.82rem;padding:12px 16px;">
            Annuler
        </a>
        <div style="margin-left:auto;">
            <form action="{{ route('annonces.destroy', $annonce) }}" method="POST" onsubmit="return confirm('Supprimer définitivement cet article ?');">
                @csrf @method('DELETE')
                <button type="submit"
                    style="background:#fef2f2;color:#ef4444;border:1.5px solid #fecaca;padding:10px 24px;border-radius:50px;font-size:.82rem;font-weight:600;cursor:pointer;font-family:inherit;">
                    <i class="fas fa-trash me-2"></i>Supprimer l'article
                </button>
            </form>
        </div>
    </div>
</form>
@endsection

@section('right-panel')
<div class="card" style="margin-bottom:16px;">
    <div style="padding:16px 18px;border-bottom:1px solid #f5f5f5;">
        <div style="font-size:.82rem;font-weight:700;color:#1a1a2e;"><i class="fas fa-info-circle me-2" style="color:#16a34a;"></i>Aperçu rapide</div>
    </div>
    <div style="padding:16px 18px;">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:14px;">
            <div style="width:48px;height:48px;background:#f0fdf4;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:1.4rem;">
                {{ $annonce->categorie->icone ?? '📦' }}
            </div>
            <div>
                <div style="font-size:.8rem;font-weight:700;color:#1a1a2e;">{{ Str::limit($annonce->titre, 22) }}</div>
                <div style="font-size:.7rem;color:#888;margin-top:2px;">
                    @php $tc=['vente'=>'pill-blue','don'=>'pill-green','alimentation_animale'=>'pill-yellow','transformation'=>'pill-gray']; @endphp
                    <span class="status-pill {{ $tc[$annonce->type_offre]??'pill-gray' }}" style="font-size:.62rem;padding:2px 7px;">{{ $annonce->type_offre }}</span>
                </div>
            </div>
        </div>
        <div style="font-size:.75rem;color:#888;display:flex;flex-direction:column;gap:6px;">
            <div><i class="fas fa-eye" style="width:14px;color:#bbb;"></i> <strong>{{ $annonce->vues }}</strong> vues</div>
            <div><i class="fas fa-balance-scale" style="width:14px;color:#bbb;"></i> {{ $annonce->quantite }} {{ $annonce->unite }}</div>
            <div><i class="fas fa-calendar" style="width:14px;color:#bbb;"></i> Créé le {{ $annonce->created_at->format('d/m/Y') }}</div>
            @if($annonce->date_expiration)
            <div><i class="fas fa-clock" style="width:14px;color:#bbb;"></i> Expire le {{ $annonce->date_expiration->format('d/m/Y') }}</div>
            @endif
        </div>
        <a href="{{ route('annonces.show', $annonce) }}"
            style="display:block;margin-top:14px;text-align:center;background:#f0fdf4;color:#16a34a;text-decoration:none;padding:8px;border-radius:8px;font-size:.75rem;font-weight:600;">
            <i class="fas fa-external-link-alt me-1"></i>Voir dans la boutique
        </a>
    </div>
</div>

<div class="card">
    <div style="padding:16px 18px;border-bottom:1px solid #f5f5f5;">
        <div style="font-size:.82rem;font-weight:700;color:#1a1a2e;"><i class="fas fa-lightbulb me-2" style="color:#f59e0b;"></i>Conseils</div>
    </div>
    <div style="padding:14px 18px;display:flex;flex-direction:column;gap:10px;">
        <div style="font-size:.75rem;color:#666;display:flex;gap:8px;align-items:flex-start;">
            <span style="color:#16a34a;font-weight:700;flex-shrink:0;">✓</span>
            Mettez à jour le statut quand votre stock change.
        </div>
        <div style="font-size:.75rem;color:#666;display:flex;gap:8px;align-items:flex-start;">
            <span style="color:#16a34a;font-weight:700;flex-shrink:0;">✓</span>
            Ajoutez une nouvelle photo si le produit a évolué.
        </div>
        <div style="font-size:.75rem;color:#666;display:flex;gap:8px;align-items:flex-start;">
            <span style="color:#16a34a;font-weight:700;flex-shrink:0;">✓</span>
            Actualisez la date d'expiration pour rester en tête des résultats.
        </div>
        <div style="font-size:.75rem;color:#666;display:flex;gap:8px;align-items:flex-start;">
            <span style="color:#f59e0b;font-weight:700;flex-shrink:0;">!</span>
            Supprimer l'article est <strong>irréversible</strong> — préférez "Expiré" pour archiver.
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
(function(){
    const initLat = {{ old('latitude', $annonce->latitude ?? 5.3484) }};
    const initLng = {{ old('longitude', $annonce->longitude ?? -4.0166) }};
    const hasCoords = {{ ($annonce->latitude) ? 'true' : 'false' }};

    const map = L.map('pickMap').setView([initLat, initLng], hasCoords ? 15 : 12);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap', maxZoom: 19
    }).addTo(map);

    let marker = null;
    if (hasCoords) {
        marker = L.marker([initLat, initLng], {draggable: true}).addTo(map);
        marker.on('dragend', () => setCoords(marker.getLatLng().lat, marker.getLatLng().lng));
    }

    map.on('click', function(e){
        const { lat, lng } = e.latlng;
        if (marker) { marker.setLatLng([lat, lng]); }
        else {
            marker = L.marker([lat, lng], {draggable: true}).addTo(map);
            marker.on('dragend', () => setCoords(marker.getLatLng().lat, marker.getLatLng().lng));
        }
        setCoords(lat, lng);
    });

    function setCoords(lat, lng){
        document.getElementById('lat_input').value = lat.toFixed(7);
        document.getElementById('lng_input').value = lng.toFixed(7);
        document.getElementById('coordsText').textContent = lat.toFixed(5) + ', ' + lng.toFixed(5);
        document.getElementById('coordsDisplay').style.display = 'block';
    }

    document.getElementById('btnGeolocate').addEventListener('click', function(){
        if (!navigator.geolocation) return;
        this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        const btn = this;
        navigator.geolocation.getCurrentPosition(function(pos){
            const lat = pos.coords.latitude, lng = pos.coords.longitude;
            map.setView([lat, lng], 16);
            if (marker) { marker.setLatLng([lat, lng]); }
            else {
                marker = L.marker([lat, lng], {draggable: true}).addTo(map);
                marker.on('dragend', () => setCoords(marker.getLatLng().lat, marker.getLatLng().lng));
            }
            setCoords(lat, lng);
            btn.innerHTML = '<i class="fas fa-crosshairs"></i>';
        }, () => { btn.innerHTML = '<i class="fas fa-crosshairs"></i>'; });
    });
})();
</script>
<script>
// Type offre toggle prix
document.getElementById('typeOffre').addEventListener('change', function(){
    const isDon = this.value === 'don';
    document.getElementById('prixField').style.display = isDon ? 'none' : '';
    document.getElementById('donNote').style.display = isDon ? 'block' : 'none';
    const u = this.value;
    const labels = {vente:'FCFA/unité',alimentation_animale:'FCFA/kg',transformation:'FCFA'};
    document.getElementById('prixUnit').textContent = labels[u] || 'FCFA';
});

// Photo preview
function previewPhotos(files) {
    const grid = document.getElementById('previewGrid');
    grid.innerHTML = '';
    grid.style.display = 'grid';
    Array.from(files).forEach((file, i) => {
        const reader = new FileReader();
        reader.onload = e => {
            const wrap = document.createElement('div');
            wrap.style.cssText = 'position:relative;';
            wrap.innerHTML = `
                <img src="${e.target.result}" style="width:100%;aspect-ratio:1;object-fit:cover;border-radius:8px;border:2px solid ${i===0?'#16a34a':'#e5e7eb'};">
                ${i===0?'<span style="position:absolute;top:4px;left:4px;background:#16a34a;color:#fff;font-size:.55rem;font-weight:700;padding:2px 5px;border-radius:4px;">Principal</span>':''}
            `;
            grid.appendChild(wrap);
        };
        reader.readAsDataURL(file);
    });
}

// Drag & drop
function handleDrop(e) {
    e.preventDefault();
    document.getElementById('dropZone').style.borderColor = '#d1fae5';
    document.getElementById('dropZone').style.background = '#fafffe';
    const files = e.dataTransfer.files;
    const dt = new DataTransfer();
    Array.from(files).forEach(f => dt.items.add(f));
    document.getElementById('photoInput').files = dt.files;
    previewPhotos(files);
}
</script>
@endpush
