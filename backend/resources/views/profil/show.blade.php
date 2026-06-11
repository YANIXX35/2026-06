@extends('layouts.front')
@section('title', $user->prenom.' '.$user->nom.' — Fournisseur')

@push('styles')
<style>
.profil-hero { background:linear-gradient(135deg,#052e16 0%,#14532d 100%); padding:60px 20px 100px; text-align:center; position:relative; overflow:hidden; }
.profil-hero::after { content:''; position:absolute; inset:0; background:url("data:image/svg+xml,%3Csvg width='40' height='40' viewBox='0 0 40 40' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Cpath d='M0 40L40 0H20L0 20M40 40V20L20 40'/%3E%3C/g%3E%3C/svg%3E"); }
.profil-avatar { width:100px; height:100px; border-radius:50%; border:4px solid rgba(255,255,255,.3); object-fit:cover; margin:0 auto 16px; display:block; position:relative; z-index:1; }
.profil-avatar-ph { width:100px; height:100px; border-radius:50%; border:4px solid rgba(255,255,255,.3); background:rgba(255,255,255,.1); display:flex; align-items:center; justify-content:center; font-size:2.5rem; margin:0 auto 16px; position:relative; z-index:1; }
.profil-name { font-size:1.6rem; font-weight:900; color:#fff; position:relative; z-index:1; }
.profil-structure { font-size:.9rem; color:#86efac; margin-top:4px; position:relative; z-index:1; }
.profil-stats { display:flex; justify-content:center; gap:32px; margin-top:16px; position:relative; z-index:1; }
.profil-stat { text-align:center; }
.profil-stat-v { font-size:1.4rem; font-weight:800; color:#fff; }
.profil-stat-l { font-size:.72rem; color:#86efac; }

.profil-body { max-width:960px; margin:-48px auto 40px; padding:0 16px; }
.section-card { background:#fff; border-radius:16px; box-shadow:0 2px 12px rgba(0,0,0,.07); padding:24px; margin-bottom:20px; }
.section-title { font-size:.78rem; font-weight:800; color:#aaa; text-transform:uppercase; letter-spacing:.5px; margin-bottom:16px; }

.annonce-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(200px,1fr)); gap:14px; }
.annonce-card { border:1px solid #f0f0f0; border-radius:12px; overflow:hidden; text-decoration:none; color:inherit; transition:.2s; display:block; }
.annonce-card:hover { box-shadow:0 4px 16px rgba(0,0,0,.1); transform:translateY(-2px); }
.annonce-img { width:100%; height:110px; object-fit:cover; }
.annonce-img-ph { width:100%; height:110px; background:#f3f4f6; display:flex; align-items:center; justify-content:center; font-size:2rem; }
.annonce-body { padding:10px 12px; }
.annonce-title { font-size:.82rem; font-weight:700; color:#1a1a2e; margin-bottom:4px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
.annonce-price { font-size:.78rem; color:#16a34a; font-weight:700; }

.avis-item { padding:14px 0; border-bottom:1px solid #f5f5f5; }
.avis-item:last-child { border-bottom:none; }
.stars { color:#f59e0b; font-size:.85rem; }
.avis-meta { font-size:.74rem; color:#aaa; margin-top:3px; }
.avis-text { font-size:.83rem; color:#555; margin-top:6px; line-height:1.6; }
</style>
@endpush

@section('content')
<div class="profil-hero">
    @if($user->photo)
        <img src="{{ asset('storage/'.$user->photo) }}" class="profil-avatar" alt="{{ $user->prenom }}">
    @else
        <div class="profil-avatar-ph">{{ mb_strtoupper(mb_substr($user->prenom, 0, 1)) }}</div>
    @endif
    <div class="profil-name">{{ $user->prenom }} {{ $user->nom }}</div>
    @if($user->nom_structure)
        <div class="profil-structure">🏪 {{ $user->nom_structure }}</div>
    @endif
    <div class="profil-stats">
        <div class="profil-stat">
            <div class="profil-stat-v">{{ $annonces->count() }}</div>
            <div class="profil-stat-l">Annonces actives</div>
        </div>
        <div class="profil-stat">
            <div class="profil-stat-v">{{ number_format($user->note_moyenne ?? 0, 1) }}/5</div>
            <div class="profil-stat-l">Note moyenne</div>
        </div>
        <div class="profil-stat">
            <div class="profil-stat-v">{{ $avis->count() }}</div>
            <div class="profil-stat-l">Avis reçus</div>
        </div>
    </div>
</div>

<div class="profil-body">
    <div class="row g-4">

        <div class="col-lg-8">
            {{-- Description --}}
            @if($user->description_structure)
            <div class="section-card">
                <div class="section-title"><i class="fas fa-info-circle me-2"></i>À propos</div>
                <p style="font-size:.88rem;color:#555;line-height:1.7;margin:0;">{{ $user->description_structure }}</p>
            </div>
            @endif

            {{-- Annonces actives --}}
            <div class="section-card">
                <div class="section-title"><i class="fas fa-bullhorn me-2"></i>Annonces disponibles</div>
                @if($annonces->count())
                <div class="annonce-grid">
                    @foreach($annonces as $annonce)
                    <a href="{{ route('annonces.show', $annonce) }}" class="annonce-card">
                        @if($annonce->photoPrincipale)
                            <img src="{{ asset('storage/'.$annonce->photoPrincipale->url) }}" class="annonce-img"
                                 alt="{{ $annonce->titre }}" onerror="this.src='{{ asset('img/no-image.jpg') }}'">
                        @else
                            <div class="annonce-img-ph">{{ $annonce->categorie->icone ?? '📦' }}</div>
                        @endif
                        <div class="annonce-body">
                            <div class="annonce-title">{{ $annonce->titre }}</div>
                            <div class="annonce-price">
                                @if($annonce->type_offre === 'don') Gratuit
                                @else {{ number_format($annonce->prix, 0, ',', ' ') }} F/{{ $annonce->unite }}
                                @endif
                            </div>
                        </div>
                    </a>
                    @endforeach
                </div>
                @else
                <p class="text-muted text-center mb-0" style="font-size:.85rem;">Aucune annonce disponible pour le moment.</p>
                @endif
            </div>

            {{-- Avis --}}
            <div class="section-card">
                <div class="section-title"><i class="fas fa-star me-2"></i>Avis clients ({{ $avis->count() }})</div>
                @forelse($avis as $av)
                <div class="avis-item">
                    <div class="d-flex align-items-center gap-3">
                        <div style="width:36px;height:36px;border-radius:50%;background:#f0fdf4;display:flex;align-items:center;justify-content:center;font-size:.85rem;font-weight:700;color:#16a34a;flex-shrink:0;">
                            {{ mb_strtoupper(mb_substr($av->auteur->prenom ?? '?', 0, 1)) }}
                        </div>
                        <div>
                            <div style="font-weight:700;font-size:.83rem;">{{ $av->auteur->prenom ?? 'Utilisateur' }} {{ $av->auteur->nom ?? '' }}</div>
                            <div class="stars">
                                @for($i = 1; $i <= 5; $i++)
                                    {{ $i <= $av->note ? '★' : '☆' }}
                                @endfor
                                <span style="color:#aaa;font-size:.72rem;margin-left:4px;">{{ $av->created_at->format('d/m/Y') }}</span>
                            </div>
                        </div>
                    </div>
                    @if($av->commentaire)
                        <div class="avis-text">{{ $av->commentaire }}</div>
                    @endif
                </div>
                @empty
                <p class="text-muted text-center mb-0" style="font-size:.85rem;">Aucun avis pour l'instant.</p>
                @endforelse
            </div>
        </div>

        <div class="col-lg-4">
            {{-- Fiche contact --}}
            <div class="section-card" style="position:sticky;top:90px;">
                <div class="section-title"><i class="fas fa-address-card me-2"></i>Contact</div>
                <div style="font-size:.85rem;color:#555;line-height:2.2;">
                    @if($user->type_structure)
                        <div><i class="fas fa-building me-2" style="color:#9ca3af;width:16px;"></i>{{ ucfirst($user->type_structure) }}</div>
                    @endif
                    @if($user->adresse)
                        <div><i class="fas fa-map-marker-alt me-2" style="color:#9ca3af;width:16px;"></i>{{ $user->adresse }}</div>
                    @endif
                    @if($user->telephone)
                        <div><i class="fas fa-phone me-2" style="color:#9ca3af;width:16px;"></i>{{ $user->telephone }}</div>
                    @endif
                    <div><i class="fas fa-calendar me-2" style="color:#9ca3af;width:16px;"></i>Membre depuis {{ $user->created_at->format('M Y') }}</div>
                </div>

                @auth
                @if(Auth::id() !== $user->id)
                <a href="{{ route('messages.ouvrir', $user->id) }}"
                   style="display:flex;align-items:center;justify-content:center;gap:8px;
                          background:#16a34a;color:#fff;border-radius:50px;padding:12px;
                          text-decoration:none;font-weight:700;font-size:.85rem;margin-top:16px;
                          transition:.2s;" onmouseover="this.style.background='#15803d'" onmouseout="this.style.background='#16a34a'">
                    <i class="fas fa-comment"></i> Envoyer un message
                </a>
                @endif
                @endauth
            </div>
        </div>

    </div>
</div>
@endsection
