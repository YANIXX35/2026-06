@extends('layouts.app')
@section('title', 'Paiement — Prix négocié')

@push('styles')
<style>
.pay-neg-wrapper {
    min-height: 100vh;
    background: linear-gradient(135deg, #f0fdf4 0%, #f8fafc 50%, #eff6ff 100%);
    padding: 40px 16px 80px;
}
.pay-neg-container {
    max-width: 840px;
    margin: 0 auto;
    display: grid;
    grid-template-columns: 1fr 380px;
    gap: 24px;
    align-items: start;
}
@media(max-width: 768px) { .pay-neg-container { grid-template-columns: 1fr; } }

.pay-card {
    background: #fff;
    border-radius: 20px;
    padding: 28px;
    box-shadow: 0 2px 16px rgba(0,0,0,.06);
}

/* Badge négociation */
.negocie-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: linear-gradient(135deg, #dcfce7, #bbf7d0);
    border: 1.5px solid #86efac;
    color: #15803d;
    font-size: .78rem;
    font-weight: 700;
    padding: 5px 14px;
    border-radius: 50px;
    margin-bottom: 10px;
}

/* Prix comparatif */
.prix-compare {
    background: #f8fafc;
    border: 1.5px solid #e2e8f0;
    border-radius: 14px;
    padding: 16px 20px;
    margin-bottom: 20px;
}
.prix-barre {
    font-size: .9rem;
    color: #94a3b8;
    text-decoration: line-through;
}
.prix-negocie {
    font-size: 2.2rem;
    font-weight: 900;
    color: #15803d;
    letter-spacing: -1px;
    line-height: 1;
}
.prix-economie {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    background: #dcfce7;
    color: #15803d;
    font-size: .78rem;
    font-weight: 700;
    padding: 3px 10px;
    border-radius: 50px;
    margin-top: 6px;
}

/* Modes de paiement */
.pay-method-btn {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 16px;
    border: 2px solid #e5e7eb;
    border-radius: 14px;
    cursor: pointer;
    transition: all .2s;
    background: #fff;
    width: 100%;
    margin-bottom: 10px;
}
.pay-method-btn:hover { border-color: #86efac; }
.pay-method-btn.selected { border-color: #16a34a; background: #f0fdf4; }
.pay-method-btn input[type=radio] { display: none; }
</style>
@endpush

@section('content')
<div class="pay-neg-wrapper">

    {{-- En-tête --}}
    <div style="text-align:center;margin-bottom:28px;">
        <span class="negocie-badge"><i class="fas fa-handshake"></i>Prix négocié</span>
        <h1 style="font-size:1.6rem;font-weight:900;color:#111827;margin-bottom:4px;">
            Finaliser votre achat
        </h1>
        <p style="color:#6b7280;font-size:.9rem;">
            Vous payez le prix que vous avez négocié avec le fournisseur.
        </p>
    </div>

    <div class="pay-neg-container">

        {{-- ── COLONNE GAUCHE : récapitulatif ── --}}
        <div>
            {{-- Carte annonce + prix --}}
            <div class="pay-card mb-4">
                <div class="d-flex align-items-start gap-3 mb-4">
                    @if($reservation->annonce->photoPrincipale)
                        <img src="{{ $reservation->annonce->photoPrincipale->url }}"
                             alt="{{ $reservation->annonce->titre }}"
                             style="width:72px;height:72px;object-fit:cover;border-radius:12px;flex-shrink:0;"
                             onerror="this.src='{{ $reservation->annonce->default_image_by_category }}'">
                    @endif
                    <div>
                        <div class="fw-bold" style="font-size:1rem;color:#111827;">{{ $reservation->annonce->titre }}</div>
                        <div class="text-muted small">
                            <i class="fas fa-store me-1"></i>
                            {{ $reservation->annonce->user->nom_structure ?? $reservation->annonce->user->prenom . ' ' . $reservation->annonce->user->nom }}
                        </div>
                        <div class="text-muted small"><i class="fas fa-map-marker-alt me-1"></i>{{ $reservation->annonce->adresse_collecte }}</div>
                    </div>
                </div>

                {{-- Comparatif prix --}}
                <div class="prix-compare">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="text-muted small fw-semibold">Prix affiché sur l'annonce</span>
                        <span class="prix-barre">{{ number_format($reservation->annonce->prix, 0, ',', ' ') }} FCFA</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-baseline">
                        <span class="fw-bold text-success small">Votre prix négocié</span>
                        <div class="text-end">
                            <div class="prix-negocie">{{ number_format($reservation->prix_negocie, 0, ',', ' ') }} <span style="font-size:1rem;">FCFA</span></div>
                        </div>
                    </div>
                    @php
                        $economie    = $reservation->annonce->prix - $reservation->prix_negocie;
                        $economiePct = $reservation->annonce->prix > 0 ? round($economie / $reservation->annonce->prix * 100) : 0;
                    @endphp
                    @if($economie > 0)
                    <div>
                        <span class="prix-economie">
                            <i class="fas fa-piggy-bank"></i>
                            Vous économisez {{ number_format($economie, 0, ',', ' ') }} FCFA ({{ $economiePct }}%) !
                        </span>
                    </div>
                    @endif
                </div>

                {{-- Détail ligne --}}
                <div style="background:#f8fafc;border-radius:12px;padding:14px 16px;">
                    <div class="d-flex justify-content-between mb-1 small text-muted">
                        <span>Quantité</span>
                        <span class="fw-semibold text-dark">{{ $reservation->quantite_demandee }} {{ $reservation->annonce->unite }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-1 small text-muted">
                        <span>Prix unitaire négocié</span>
                        <span class="fw-semibold text-dark">{{ number_format($reservation->prix_negocie, 0, ',', ' ') }} FCFA</span>
                    </div>
                    <hr style="margin:8px 0;">
                    <div class="d-flex justify-content-between fw-bold">
                        <span>Total à payer</span>
                        <span class="text-success" style="font-size:1.1rem;">
                            {{ number_format($reservation->prix_negocie * $reservation->quantite_demandee, 0, ',', ' ') }} FCFA
                        </span>
                    </div>
                </div>

                {{-- Badge preuve négociation --}}
                @if($reservation->offre)
                <div class="mt-3 d-flex align-items-center gap-2" style="font-size:.76rem;color:#64748b;">
                    <i class="fas fa-shield-check text-success"></i>
                    Accord tracé en base de données · Offre #{{ $reservation->offre->id }} ·
                    Acceptée le {{ $reservation->offre->updated_at->format('d/m/Y à H:i') }}
                </div>
                @endif
            </div>

            {{-- Avertissement anti-arnaque --}}
            <div style="background:#fefce8;border:1.5px solid #fbbf24;border-radius:12px;padding:12px 16px;font-size:.78rem;color:#92400e;">
                <i class="fas fa-shield-alt me-2 text-warning"></i>
                Ce paiement correspond à votre accord négocié via AntiGaspiCI.
                Le prix de {{ number_format($reservation->annonce->prix, 0, ',', ' ') }} FCFA reste visible pour les autres acheteurs — seul vous bénéficiez du prix négocié.
            </div>
        </div>

        {{-- ── COLONNE DROITE : formulaire de paiement ── --}}
        <div>
            <div class="pay-card">
                <h6 class="fw-bold mb-4" style="color:#111827;">
                    <i class="fas fa-mobile-alt me-2 text-primary"></i>Choisir un moyen de paiement
                </h6>

                <form action="{{ route('paiement.reservation.confirmer', $reservation) }}" method="POST" id="form-paiement-negocie">
                    @csrf

                    {{-- Wave --}}
                    <label class="pay-method-btn" id="label-wave" onclick="selectMethod('wave')">
                        <input type="radio" name="mode_paiement" value="wave">
                        <div style="width:42px;height:42px;border-radius:10px;background:#FF6B00;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <span style="color:#fff;font-weight:900;font-size:.75rem;">W</span>
                        </div>
                        <div>
                            <div class="fw-bold" style="font-size:.9rem;">Wave</div>
                            <div class="text-muted" style="font-size:.75rem;">Paiement Mobile Money (simulé)</div>
                        </div>
                    </label>

                    {{-- Moov --}}
                    <label class="pay-method-btn" id="label-moov" onclick="selectMethod('moov_money')">
                        <input type="radio" name="mode_paiement" value="moov_money">
                        <div style="width:42px;height:42px;border-radius:10px;background:#003DA5;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <span style="color:#fff;font-weight:900;font-size:.65rem;">MOOV</span>
                        </div>
                        <div>
                            <div class="fw-bold" style="font-size:.9rem;">Moov Money</div>
                            <div class="text-muted" style="font-size:.75rem;">Paiement Mobile Money (simulé)</div>
                        </div>
                    </label>

                    <div class="mb-3 mt-3" id="champ-telephone" style="display:none;">
                        <label class="form-label small fw-semibold">Numéro de téléphone</label>
                        <div class="input-group">
                            <span class="input-group-text">+225</span>
                            <input type="tel" name="telephone" class="form-control"
                                placeholder="07 00 00 00 00"
                                pattern="^\+\d{3}\d{10}$"
                                id="tel-input">
                        </div>
                        <small class="text-muted">Format : +225 suivi de 10 chiffres</small>
                    </div>

                    {{-- Total récapitulatif dans le bouton --}}
                    <div style="background:#f0fdf4;border-radius:12px;padding:12px 16px;margin-bottom:16px;text-align:center;">
                        <div class="text-muted small">Montant total à confirmer</div>
                        <div style="font-size:1.6rem;font-weight:900;color:#15803d;">
                            {{ number_format($reservation->prix_negocie * $reservation->quantite_demandee, 0, ',', ' ') }} FCFA
                        </div>
                        <div class="text-muted" style="font-size:.72rem;">Prix négocié · Accord #{{ $reservation->offre_id ?? 'N/A' }}</div>
                    </div>

                    <button type="submit" id="btn-payer" class="btn btn-success w-100 rounded-pill fw-bold py-3" disabled>
                        <i class="fas fa-lock me-2"></i>Confirmer le paiement
                    </button>
                </form>

                <div class="text-center mt-3" style="font-size:.72rem;color:#94a3b8;">
                    <i class="fas fa-info-circle me-1"></i>
                    Paiement simulé — aucun débit réel. Version de démonstration académique.
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let selectedMethod = null;

function selectMethod(method) {
    selectedMethod = method;
    document.querySelectorAll('.pay-method-btn').forEach(el => el.classList.remove('selected'));
    document.getElementById('label-' + (method === 'wave' ? 'wave' : 'moov')).classList.add('selected');

    // Afficher champ téléphone
    document.getElementById('champ-telephone').style.display = 'block';

    // Pré-remplir préfixe
    const tel = document.getElementById('tel-input');
    if (!tel.value) tel.value = '+225';

    updateButton();
}

function updateButton() {
    const tel   = document.getElementById('tel-input').value;
    const valid = selectedMethod && /^\+\d{13}$/.test(tel.replace(/\s/g, ''));
    document.getElementById('btn-payer').disabled = !valid;
}

document.getElementById('tel-input')?.addEventListener('input', updateButton);
</script>
@endpush
@endsection
