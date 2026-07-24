@extends('layouts.app')
@section('title', 'Conversation')

@push('styles')
<style>
/* ── NÉGOCIATION : FIL D'OFFRES ── */
.offre-bubble {
    border-radius: 14px;
    padding: 14px 16px;
    margin-bottom: 10px;
    position: relative;
    font-size: .87rem;
}
.offre-bubble.acheteur  { background: #eff6ff; border: 1.5px solid #bfdbfe; }
.offre-bubble.fournisseur { background: #f0fdf4; border: 1.5px solid #bbf7d0; }
.offre-bubble.historique  { background: #f9fafb; border: 1.5px dashed #d1d5db; opacity: .65; }

.offre-prix-badge {
    font-size: 1.15rem;
    font-weight: 800;
    letter-spacing: -.5px;
}
.offre-delta {
    font-size: .72rem;
    font-weight: 600;
    padding: 2px 7px;
    border-radius: 50px;
}
.offre-delta.baisse { background: #dcfce7; color: #15803d; }
.offre-delta.hausse { background: #fee2e2; color: #b91c1c; }
.offre-delta.egal   { background: #f1f5f9; color: #475569; }

.anti-arnaque-banner {
    background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
    border: 1.5px solid #f59e0b;
    border-radius: 12px;
    padding: 12px 16px;
    margin-bottom: 14px;
    font-size: .80rem;
}

/* Formulaire proposition prix */
.form-offre-card {
    background: #fff;
    border: 2px solid #e0e7ff;
    border-radius: 16px;
    padding: 18px;
    margin-bottom: 14px;
    box-shadow: 0 2px 10px rgba(79,70,229,.05);
}
.form-offre-card.contre-offre-form {
    border-color: #bbf7d0;
    background: #f0fdf4;
}

.btn-contre-offre-toggle {
    font-size: .78rem;
    color: #16a34a;
    background: none;
    border: none;
    text-decoration: underline;
    cursor: pointer;
    padding: 0;
}
.contre-offre-area { display: none; }
.contre-offre-area.visible { display: block; }
</style>
@endpush

@section('content')
<div class="container py-5">
    <div class="row">
        {{-- ── SIDEBAR : liste conversations ── --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-primary text-white p-4">
                    <h6 class="mb-0 fw-bold"><i class="fas fa-comments me-2"></i>Conversations</h6>
                </div>
                <div class="card-body p-0">
                    @forelse($conversations as $conv)
                        @php
                            $autre2 = $conv->user_1_id === auth()->id() ? $conv->user2 : $conv->user1;
                            $dernier2 = $conv->dernierMessage;
                            $nonLus2 = $conv->nonLus(auth()->id());
                        @endphp
                        <a href="{{ route('messages.show', $conv) }}" class="text-decoration-none">
                            <div class="d-flex align-items-center p-3 border-bottom {{ $conv->id === $conversation->id ? 'bg-primary bg-opacity-10' : '' }}">
                                <div class="rounded-circle bg-{{ $conv->id === $conversation->id ? 'primary' : 'secondary' }} d-flex align-items-center justify-content-center me-3 flex-shrink-0" style="width:40px;height:40px;">
                                    <span class="text-white fw-bold small">{{ strtoupper(substr($autre2->prenom,0,1)) }}</span>
                                </div>
                                <div class="flex-grow-1 min-w-0">
                                    <div class="fw-semibold small text-dark">{{ $autre2->prenom }} {{ $autre2->nom }}</div>
                                    @if($dernier2)
                                        <div class="text-muted" style="font-size:11px;">{{ Str::limit($dernier2->contenu, 35) }}</div>
                                    @endif
                                    @if($conv->annonce)
                                        <div class="text-primary" style="font-size:10px;"><i class="fas fa-bullhorn me-1"></i>{{ Str::limit($conv->annonce->titre, 28) }}</div>
                                    @endif
                                </div>
                                @if($nonLus2 > 0)
                                    <span class="badge bg-primary rounded-pill ms-1">{{ $nonLus2 }}</span>
                                @endif
                            </div>
                        </a>
                    @empty
                        <div class="text-center py-4 text-muted small">Aucune conversation</div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- ── CHAT PRINCIPAL ── --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-3">

                {{-- En-tête interlocuteur --}}
                <div class="card-header bg-white p-4 border-bottom d-flex align-items-center">
                    <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-3" style="width:44px;height:44px;">
                        <span class="text-white fw-bold">{{ strtoupper(substr($interlocuteur->prenom,0,1)) }}</span>
                    </div>
                    <div>
                        <div class="fw-bold">{{ $interlocuteur->prenom }} {{ $interlocuteur->nom }}</div>
                        <div class="text-muted small">{{ $interlocuteur->role }}</div>
                    </div>
                </div>

                {{-- Bandeau lien annonce --}}
                @if($conversation->annonce)
                <a href="{{ route('annonces.show', $conversation->annonce) }}" class="d-flex align-items-center gap-2 px-4 py-2 border-bottom text-decoration-none" style="background:#f8f9fb;">
                    <i class="fas fa-bullhorn text-muted small"></i>
                    <span class="small text-muted">À propos de :</span>
                    <span class="small fw-semibold text-dark">{{ Str::limit($conversation->annonce->titre, 50) }}</span>
                    @if($conversation->annonce->type_offre === 'vente')
                        <span class="ms-auto small fw-bold text-primary">{{ number_format($conversation->annonce->prix, 0, ',', ' ') }} FCFA</span>
                    @endif
                </a>
                @endif

                {{-- ══ SECTION NÉGOCIATION ══ --}}
                @if($conversation->annonce && $conversation->annonce->type_offre === 'vente')
                <div class="px-4 pt-4 pb-2">

                    {{-- ── Bandeau anti-arnaque ── --}}
                    <div class="anti-arnaque-banner">
                        <div class="d-flex align-items-start gap-2">
                            <i class="fas fa-shield-alt text-warning mt-1" style="font-size:1rem;"></i>
                            <div>
                                <strong class="d-block" style="font-size:.82rem;">🛡️ Négociez uniquement dans l'application</strong>
                                <span class="text-dark" style="font-size:.77rem;">
                                    Seules les offres passées via ce formulaire sont tracées et peuvent être produites en cas de litige.
                                    Tout accord conclu <strong>hors application</strong> (WhatsApp, appel, rencontre directe) <strong>n'est couvert par aucune garantie AntiGaspiCI.</strong>
                                    <a href="{{ route('cgu') }}" class="text-warning fw-semibold text-decoration-underline ms-1">En savoir plus (CGU)</a>
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- ── Fil des offres ── --}}
                    @if($offres->count())
                        <div class="mb-3">
                            <div class="small text-muted fw-semibold mb-2 text-uppercase" style="letter-spacing:.5px;font-size:.72rem;">
                                <i class="fas fa-history me-1"></i>Historique de la négociation
                            </div>

                            @php $prixInitial = $conversation->annonce->prix; @endphp

                            @foreach($offres as $offre)
                                @php
                                    $badgeOffre = [
                                        'en_attente' => ['warning',  'En attente'],
                                        'acceptee'   => ['success',  'Acceptée ✅'],
                                        'refusee'    => ['danger',   'Refusée ❌'],
                                        'remplacee'  => ['secondary','Remplacée'],
                                        'expiree'    => ['secondary','Expirée'],
                                    ];
                                    [$couleurOffre, $labelOffre] = $badgeOffre[$offre->statut] ?? ['secondary', $offre->statut];

                                    $proposeurId = $offre->proposeur_id ?? $offre->acheteur_id;
                                    $isMine      = $proposeurId === auth()->id();
                                    $estContre   = $offre->est_contre_offre;

                                    // Delta prix vs annonce initiale
                                    $delta   = (float)$offre->prix_propose - (float)$prixInitial;
                                    $deltaPct = $prixInitial > 0 ? round(abs($delta) / $prixInitial * 100) : 0;
                                    $deltaClass = $delta < 0 ? 'baisse' : ($delta > 0 ? 'hausse' : 'egal');
                                    $deltaLabel = $delta < 0
                                        ? '-'.$deltaPct.'% vs prix initial'
                                        : ($delta > 0 ? '+'.$deltaPct.'% vs prix initial' : 'Prix initial');

                                    $proposeurNom = $offre->proposeur_id
                                        ? ($offre->proposeur->prenom ?? '—')
                                        : ($offre->acheteur->prenom ?? '—');

                                    $isHistorique = in_array($offre->statut, ['remplacee', 'expiree']);
                                @endphp

                                <div class="offre-bubble {{ $isMine ? ($estContre ? 'fournisseur' : 'acheteur') : ($estContre ? 'acheteur' : 'fournisseur') }} {{ $isHistorique ? 'historique' : '' }}">
                                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                                        <div>
                                            <div class="small fw-semibold mb-1" style="color:#475569;">
                                                {{ $estContre ? '🔄 Contre-offre de' : '💬 Offre de' }}
                                                <strong>{{ $proposeurNom }}</strong>
                                                <span class="text-muted fw-normal ms-1" style="font-size:.7rem;">{{ $offre->created_at->diffForHumans() }}</span>
                                            </div>
                                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                                <span class="offre-prix-badge text-primary">{{ number_format($offre->prix_propose, 0, ',', ' ') }} FCFA</span>
                                                <span class="offre-delta {{ $deltaClass }}">{{ $deltaLabel }}</span>
                                                <span class="text-muted small">· {{ $offre->quantite }} {{ $conversation->annonce->unite ?? '' }}</span>
                                            </div>
                                            @if($offre->message)
                                                <div class="text-muted mt-1 fst-italic" style="font-size:.8rem;">« {{ $offre->message }} »</div>
                                            @endif
                                        </div>
                                        <span class="badge bg-{{ $couleurOffre }} rounded-pill">{{ $labelOffre }}</span>
                                    </div>

                                    {{-- Boutons d'action selon le rôle et le statut --}}
                                    @if($offre->statut === 'en_attente')

                                        {{-- Fournisseur face à une offre initiale de l'acheteur --}}
                                        @if(!$offre->est_contre_offre && $offre->fournisseur_id === auth()->id())
                                        <div class="mt-3 d-flex gap-2 flex-wrap align-items-center">
                                            <form action="{{ route('offres.accepter', $offre) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success rounded-pill px-3 fw-semibold">
                                                    <i class="fas fa-check me-1"></i>Accepter
                                                </button>
                                            </form>
                                            <form action="{{ route('offres.refuser', $offre) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3">
                                                    <i class="fas fa-times me-1"></i>Refuser
                                                </button>
                                            </form>
                                            <form action="{{ route('offres.maintenir', $offre) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                                                    <i class="fas fa-thumbtack me-1"></i>Maintenir le prix initial
                                                </button>
                                            </form>
                                            <button class="btn-contre-offre-toggle" onclick="toggleContreOffre({{ $offre->id }})">
                                                <i class="fas fa-reply me-1"></i>Proposer un autre prix
                                            </button>
                                        </div>

                                        {{-- Formulaire contre-offre (masqué par défaut) --}}
                                        <div id="contre-offre-{{ $offre->id }}" class="contre-offre-area mt-3">
                                            <div class="form-offre-card contre-offre-form p-3">
                                                <div class="small fw-semibold text-success mb-2"><i class="fas fa-reply me-1"></i>Votre contre-offre</div>
                                                <form action="{{ route('offres.contre-offrir', $offre) }}" method="POST">
                                                    @csrf
                                                    <div class="d-flex gap-2 flex-wrap align-items-end">
                                                        <div>
                                                            <label class="form-label small mb-1">Votre prix (FCFA)</label>
                                                            <input type="number" name="prix_propose" class="form-control form-control-sm" style="width:140px;" step="1" min="1" placeholder="ex: {{ round($offre->prix_propose * 1.1) }}" required>
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <label class="form-label small mb-1">Message (optionnel)</label>
                                                            <input type="text" name="message" class="form-control form-control-sm" placeholder="Expliquez votre contre-offre..." maxlength="500">
                                                        </div>
                                                        <button type="submit" class="btn btn-sm btn-success rounded-pill px-3 fw-semibold" style="white-space:nowrap;">
                                                            <i class="fas fa-paper-plane me-1"></i>Envoyer
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                        @endif

                                        {{-- Acheteur face à une contre-offre du fournisseur --}}
                                        @if($offre->est_contre_offre && $offre->acheteur_id === auth()->id())
                                        <div class="mt-3 d-flex gap-2 flex-wrap align-items-center">
                                            <form action="{{ route('offres.accepter', $offre) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success rounded-pill px-3 fw-semibold">
                                                    <i class="fas fa-check me-1"></i>Accepter et payer
                                                </button>
                                            </form>
                                            <form action="{{ route('offres.refuser', $offre) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3">
                                                    <i class="fas fa-times me-1"></i>Refuser
                                                </button>
                                            </form>
                                            <span class="small text-muted ms-1"><i class="fas fa-info-circle me-1"></i>Vous pouvez aussi envoyer une nouvelle offre ci-dessous</span>
                                        </div>
                                        @endif

                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif

                    {{-- ── Formulaire de proposition de prix (acheteur uniquement) ── --}}
                    @if($conversation->annonce->user_id !== auth()->id())
                        @php
                            $offreActiveExiste = $offres->whereIn('statut', ['en_attente'])->count() > 0;
                            $prixAnnonce = $conversation->annonce->prix;
                        @endphp
                        <div class="form-offre-card">
                            <div class="d-flex align-items-center gap-2 mb-3">
                                <i class="fas fa-hand-holding-usd text-primary" style="font-size:1.1rem;"></i>
                                <span class="fw-semibold" style="font-size:.92rem;">
                                    {{ $offreActiveExiste ? 'Proposer un nouveau prix' : 'Proposer un prix' }}
                                </span>
                                <span class="ms-auto small text-muted">
                                    Prix affiché : <strong class="text-primary">{{ number_format($prixAnnonce, 0, ',', ' ') }} FCFA</strong>
                                </span>
                            </div>

                            @if($offreActiveExiste)
                            <div class="alert alert-info py-2 px-3 rounded-3 mb-3" style="font-size:.8rem;">
                                <i class="fas fa-clock me-1"></i> Une offre est déjà en attente. En soumettant une nouvelle offre, l'ancienne sera automatiquement annulée.
                            </div>
                            @endif

                            <form action="{{ route('offres.store', $conversation) }}" method="POST">
                                @csrf
                                <div class="row g-2 mb-2">
                                    <div class="col-sm-5">
                                        <label class="form-label small mb-1 fw-semibold">Votre prix proposé (FCFA)</label>
                                        <div class="input-group input-group-sm">
                                            <input type="number" name="prix_propose" id="prix_propose_input"
                                                class="form-control"
                                                step="1" min="1"
                                                placeholder="ex: {{ round($prixAnnonce * 0.8) }}"
                                                oninput="updateDelta(this.value)"
                                                required>
                                            <span class="input-group-text">FCFA</span>
                                        </div>
                                        <div id="prix-delta-preview" class="mt-1" style="font-size:.75rem;min-height:18px;"></div>
                                    </div>
                                    <div class="col-sm-4">
                                        <label class="form-label small mb-1 fw-semibold">Quantité ({{ $conversation->annonce->unite ?? 'unité' }})</label>
                                        <input type="number" name="quantite" class="form-control form-control-sm"
                                            step="0.01" min="0.01"
                                            max="{{ $conversation->annonce->quantite }}"
                                            value="{{ $conversation->annonce->quantite }}"
                                            required>
                                        <div class="text-muted" style="font-size:.72rem;">Max : {{ $conversation->annonce->quantite }} {{ $conversation->annonce->unite ?? '' }}</div>
                                    </div>
                                    <div class="col-sm-3 d-flex align-items-end">
                                        <button type="submit" class="btn btn-primary w-100 btn-sm rounded-pill fw-semibold" style="white-space:nowrap;">
                                            <i class="fas fa-paper-plane me-1"></i>Envoyer
                                        </button>
                                    </div>
                                </div>
                                <div>
                                    <input type="text" name="message" class="form-control form-control-sm"
                                        placeholder="💬 Message optionnel : expliquez votre offre au fournisseur..."
                                        maxlength="500">
                                </div>
                            </form>
                        </div>
                    @endif

                </div>
                @endif
                {{-- ══ FIN NÉGOCIATION ══ --}}

                {{-- ── MESSAGES TEXTE ── --}}
                <div class="card-body p-4" style="height:380px;overflow-y:auto;" id="messagesContainer">
                    @forelse($messages as $msg)
                        @if($msg->user_id === auth()->id())
                            <div class="d-flex justify-content-end mb-3">
                                <div class="bg-primary text-white rounded-3 px-3 py-2" style="max-width:70%;">
                                    <div>{{ $msg->contenu }}</div>
                                    <div style="font-size:10px;opacity:0.75;text-align:right;" class="mt-1">{{ $msg->created_at->format('H:i') }}</div>
                                </div>
                            </div>
                        @else
                            <div class="d-flex justify-content-start mb-3">
                                <div class="bg-light rounded-3 px-3 py-2" style="max-width:70%;">
                                    <div class="text-dark">{{ $msg->contenu }}</div>
                                    <div style="font-size:10px;color:#999;text-align:right;" class="mt-1">{{ $msg->created_at->format('H:i') }}</div>
                                </div>
                            </div>
                        @endif
                    @empty
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-comment fa-3x mb-3 opacity-25"></i>
                            <p>Démarrez la conversation !</p>
                        </div>
                    @endforelse
                </div>

                {{-- Zone de saisie message --}}
                <div class="card-footer bg-white p-3 border-top">
                    <form action="{{ route('messages.envoyer', $conversation) }}" method="POST" class="d-flex gap-2">
                        @csrf
                        <input type="text" name="contenu" class="form-control rounded-pill" placeholder="Écrire un message..." required autocomplete="off">
                        <button type="submit" class="btn btn-primary rounded-pill px-4">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Scroll auto vers le bas
    const container = document.getElementById('messagesContainer');
    if (container) container.scrollTop = container.scrollHeight;

    // Afficher/masquer le formulaire de contre-offre
    function toggleContreOffre(offreId) {
        const el = document.getElementById('contre-offre-' + offreId);
        if (el) el.classList.toggle('visible');
    }

    // Prévisualisation du delta de prix en temps réel
    const prixInitial = {{ (float) ($conversation->annonce?->prix ?? 0) }};

    function updateDelta(valeur) {
        const el = document.getElementById('prix-delta-preview');
        if (!el || !prixInitial) return;
        const prix = parseFloat(valeur);
        if (isNaN(prix) || prix <= 0) { el.innerHTML = ''; return; }
        const delta = prix - prixInitial;
        const pct   = Math.round(Math.abs(delta) / prixInitial * 100);
        if (delta < 0) {
            el.innerHTML = `<span style="color:#15803d;font-weight:600;">↓ ${pct}% moins cher que le prix affiché</span>`;
        } else if (delta > 0) {
            el.innerHTML = `<span style="color:#b91c1c;font-weight:600;">↑ ${pct}% au-dessus du prix affiché</span>`;
        } else {
            el.innerHTML = `<span style="color:#64748b;">= Prix identique à l'annonce</span>`;
        }
    }
</script>
@endpush
@endsection
