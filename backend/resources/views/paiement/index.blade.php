@extends('layouts.app')
@section('title', 'Paiement sécurisé')

@push('styles')
<style>
:root {
    --wave-color: #FF6B00;
    --moov-color: #003DA5;
    --green: #16a34a;
    --green-light: #f0fdf4;
}

.pay-wrapper {
    min-height: 100vh;
    background: linear-gradient(135deg, #f0fdf4 0%, #f8fafc 50%, #eff6ff 100%);
    padding: 40px 16px 80px;
}

.pay-container {
    max-width: 1000px;
    margin: 0 auto;
    display: grid;
    grid-template-columns: 1fr 420px;
    gap: 28px;
    align-items: start;
}
@media(max-width: 768px) {
    .pay-container { grid-template-columns: 1fr; }
}

/* Header */
.pay-header {
    text-align: center;
    margin-bottom: 32px;
}
.pay-header .secure-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
    color: #16a34a;
    font-size: .75rem;
    font-weight: 700;
    padding: 5px 14px;
    border-radius: 50px;
    margin-bottom: 12px;
}
.pay-header h1 {
    font-size: 1.6rem;
    font-weight: 900;
    color: #111827;
    margin-bottom: 4px;
}
.pay-header p { color: #6b7280; font-size: .9rem; }

/* Cards */
.pay-card {
    background: #fff;
    border-radius: 20px;
    box-shadow: 0 2px 20px rgba(0,0,0,.08);
    overflow: hidden;
    margin-bottom: 20px;
}
.pay-card-header {
    padding: 16px 24px;
    border-bottom: 1px solid #f3f4f6;
    font-size: .8rem;
    font-weight: 800;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: 1.2px;
    display: flex;
    align-items: center;
    gap: 8px;
}
.pay-card-body { padding: 20px 24px; }

/* Order items */
.order-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px 0;
    border-bottom: 1px solid #f9fafb;
}
.order-item:last-child { border-bottom: none; }
.order-item-img {
    width: 50px; height: 50px;
    border-radius: 10px;
    object-fit: cover;
    flex-shrink: 0;
}
.order-item-placeholder {
    width: 50px; height: 50px;
    border-radius: 10px;
    background: #f3f4f6;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.order-item-name { font-size: .85rem; font-weight: 600; color: #111827; margin-bottom: 2px; }
.order-item-qty { font-size: .75rem; color: #9ca3af; }
.order-item-price { font-size: .9rem; font-weight: 800; color: var(--green); margin-left: auto; }

/* Total */
.order-total {
    display: flex; justify-content: space-between; align-items: center;
    padding: 14px 0 0;
    margin-top: 8px;
    border-top: 2px solid #f3f4f6;
}
.order-total span:first-child { font-size: .9rem; font-weight: 700; color: #374151; }
.order-total span:last-child { font-size: 1.4rem; font-weight: 900; color: #111827; }

/* Provider cards */
.providers { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 20px; }

.provider-card {
    border: 2.5px solid #e5e7eb;
    border-radius: 16px;
    padding: 16px;
    cursor: pointer;
    transition: all .25s;
    position: relative;
    overflow: hidden;
    background: #fff;
    text-align: center;
}
.provider-card:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(0,0,0,.1); }
.provider-card.active.wave {
    border-color: var(--wave-color);
    background: #fff8f3;
    box-shadow: 0 0 0 4px rgba(255,107,0,.12);
}
.provider-card.active.moov {
    border-color: var(--moov-color);
    background: #f0f4ff;
    box-shadow: 0 0 0 4px rgba(0,61,165,.12);
}
.provider-check {
    position: absolute;
    top: 8px; right: 8px;
    width: 22px; height: 22px;
    border-radius: 50%;
    background: var(--green);
    color: #fff;
    font-size: .65rem;
    display: flex; align-items: center; justify-content: center;
    opacity: 0;
    transition: opacity .2s;
}
.provider-card.active .provider-check { opacity: 1; }
.provider-logo {
    height: 42px;
    object-fit: contain;
    margin-bottom: 8px;
    display: block;
    margin-left: auto; margin-right: auto;
}
.provider-name { font-size: .8rem; font-weight: 700; color: #374151; }
.provider-tagline { font-size: .68rem; color: #9ca3af; margin-top: 2px; }

/* Wave brand */
.wave-logo-box {
    width: 64px; height: 42px;
    background: linear-gradient(135deg, #FF6B00, #FF8C00);
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 8px;
    font-size: 1.2rem; font-weight: 900; color: #fff;
    letter-spacing: -1px;
    font-family: 'Arial Black', sans-serif;
}
/* Moov brand */
.moov-logo-box {
    width: 64px; height: 42px;
    background: linear-gradient(135deg, #003DA5, #0052CC);
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 8px;
    font-size: .75rem; font-weight: 900; color: #fff;
    letter-spacing: -.5px;
    font-family: 'Arial Black', sans-serif;
}

/* Payment form */
.payment-form { display: none; }
.payment-form.visible { display: block; }

.field-group { margin-bottom: 16px; }
.field-label {
    font-size: .72rem; font-weight: 800; color: #6b7280;
    text-transform: uppercase; letter-spacing: 1px;
    display: block; margin-bottom: 6px;
}
.field-input {
    width: 100%; border: 2px solid #e5e7eb; border-radius: 12px;
    padding: 13px 16px; font-size: .95rem; outline: none;
    transition: border-color .2s; font-family: inherit;
    background: #fafafa;
}
.field-input:focus { border-color: var(--green); background: #fff; }
.field-input.wave-focus:focus { border-color: var(--wave-color); }
.field-input.moov-focus:focus { border-color: var(--moov-color); }

.phone-hint { font-size: .72rem; color: #9ca3af; margin-top: 4px; }

/* PIN field */
.pin-row { display: flex; gap: 10px; }
.pin-input {
    flex: 1; text-align: center; font-size: 1.2rem; font-weight: 700;
    letter-spacing: 8px; border: 2px solid #e5e7eb; border-radius: 12px;
    padding: 13px; outline: none; transition: border-color .2s; background: #fafafa;
    font-family: monospace;
}
.pin-input:focus { border-color: var(--green); background: #fff; }

/* Provider info banner */
.provider-info {
    border-radius: 10px; padding: 10px 14px;
    font-size: .78rem; margin-bottom: 16px;
    display: flex; align-items: center; gap: 8px;
}
.provider-info.wave { background: #fff8f3; border: 1px solid #fed7aa; color: #c2410c; }
.provider-info.moov { background: #eff6ff; border: 1px solid #bfdbfe; color: #1d4ed8; }

/* Pay button */
.btn-pay {
    width: 100%; padding: 16px; border: none; border-radius: 14px;
    font-size: 1rem; font-weight: 800; color: #fff; cursor: pointer;
    transition: all .25s; letter-spacing: .3px;
    display: flex; align-items: center; justify-content: center; gap: 10px;
}
.btn-pay.wave { background: linear-gradient(135deg, #FF6B00, #FF8C00); }
.btn-pay.wave:hover { background: linear-gradient(135deg, #e55a00, #ff7a00); transform: translateY(-2px); }
.btn-pay.moov { background: linear-gradient(135deg, #003DA5, #0052CC); }
.btn-pay.moov:hover { background: linear-gradient(135deg, #002d7a, #003fad); transform: translateY(-2px); }
.btn-pay:disabled { opacity: .6; cursor: not-allowed; transform: none; }

.security-note {
    text-align: center; font-size: .72rem; color: #9ca3af; margin-top: 10px;
    display: flex; align-items: center; justify-content: center; gap: 5px;
}

/* Processing overlay */
.processing-overlay {
    position: fixed; inset: 0; background: rgba(0,0,0,.5);
    backdrop-filter: blur(6px);
    z-index: 9999;
    display: flex; align-items: center; justify-content: center;
    opacity: 0; pointer-events: none; transition: opacity .3s;
}
.processing-overlay.visible { opacity: 1; pointer-events: all; }

.processing-card {
    background: #fff; border-radius: 24px; padding: 48px 40px;
    text-align: center; max-width: 340px; width: 90%;
    box-shadow: 0 24px 60px rgba(0,0,0,.2);
}
.processing-spinner {
    width: 72px; height: 72px; border-radius: 50%;
    border: 5px solid #f3f4f6;
    border-top-color: var(--green);
    animation: spin 1s linear infinite;
    margin: 0 auto 20px;
}
.processing-spinner.wave-spin { border-top-color: var(--wave-color); }
.processing-spinner.moov-spin { border-top-color: var(--moov-color); }
@keyframes spin { to { transform: rotate(360deg); } }

.processing-title { font-size: 1.1rem; font-weight: 800; color: #111827; margin-bottom: 6px; }
.processing-sub { font-size: .85rem; color: #6b7280; margin-bottom: 24px; }
.processing-steps { list-style: none; padding: 0; text-align: left; }
.processing-steps li {
    display: flex; align-items: center; gap: 10px;
    font-size: .82rem; color: #6b7280; padding: 6px 0;
    border-bottom: 1px solid #f9fafb;
}
.processing-steps li:last-child { border-bottom: none; }
.step-icon {
    width: 24px; height: 24px; border-radius: 50%; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center;
    font-size: .65rem;
}
.step-icon.pending { background: #f3f4f6; color: #9ca3af; }
.step-icon.loading { background: #fef3c7; color: #d97706; animation: pulse 1.5s ease-in-out infinite; }
.step-icon.done { background: #f0fdf4; color: var(--green); }
@keyframes pulse { 0%,100%{opacity:1} 50%{opacity:.5} }

/* Back link */
.back-link {
    display: inline-flex; align-items: center; gap: 7px;
    color: #6b7280; font-size: .83rem; text-decoration: none;
    margin-bottom: 24px; transition: color .2s;
}
.back-link:hover { color: var(--green); }

/* Alert */
.pay-alert {
    background: #fef2f2; border: 1px solid #fecaca; color: #dc2626;
    border-radius: 12px; padding: 12px 16px; margin-bottom: 20px;
    font-size: .85rem; display: flex; align-items: center; gap: 8px;
}
</style>
@endpush

@section('content')
<div class="pay-wrapper">
    <a href="{{ route('cart.index') }}" class="back-link d-block" style="max-width:1000px;margin:0 auto 20px;">
        <i class="fas fa-arrow-left"></i> Retour au panier
    </a>

    <div class="pay-header">
        <div class="secure-badge">
            <i class="fas fa-shield-alt"></i> Paiement 100% sécurisé
        </div>
        <h1>Finaliser votre commande</h1>
        <p>Choisissez votre moyen de paiement pour confirmer la commande</p>
    </div>

    @if(session('error'))
    <div class="pay-alert" style="max-width:1000px;margin:0 auto 20px;">
        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
    </div>
    @endif

    <div class="pay-container">
        {{-- Colonne gauche : récap commande --}}
        <div>
            <div class="pay-card">
                <div class="pay-card-header">
                    <i class="fas fa-receipt" style="color:#16a34a;"></i>
                    Récapitulatif de la commande
                </div>
                <div class="pay-card-body">
                    @foreach($items as $item)
                    <div class="order-item">
                        @if($item->annonce->photoPrincipale)
                            <img src="{{ $item->annonce->photoPrincipale->url }}" class="order-item-img" alt="{{ $item->annonce->titre }}">
                        @else
                            <div class="order-item-placeholder">
                                <i class="fas fa-box" style="color:#d1d5db;"></i>
                            </div>
                        @endif
                        <div style="flex:1;min-width:0;">
                            <div class="order-item-name">{{ Str::limit($item->annonce->titre, 45) }}</div>
                            <div class="order-item-qty">
                                {{ $item->quantite }} {{ $item->annonce->unite }}
                                · {{ $item->annonce->categorie->icone ?? '' }} {{ $item->annonce->categorie->nom ?? '' }}
                            </div>
                        </div>
                        <div class="order-item-price">
                            @if($item->annonce->type_offre === 'don')
                                <span style="color:#3b82f6;">Gratuit</span>
                            @else
                                {{ number_format($item->sousTotal(), 0, ',', ' ') }} F
                            @endif
                        </div>
                    </div>
                    @endforeach

                    <div class="order-total">
                        <span>Total à payer</span>
                        <span>{{ $total > 0 ? number_format($total, 0, ',', ' ').' FCFA' : 'Gratuit' }}</span>
                    </div>
                </div>
            </div>

            @if($adresse || $message)
            <div class="pay-card">
                <div class="pay-card-header">
                    <i class="fas fa-info-circle" style="color:#6b7280;"></i>
                    Informations de collecte
                </div>
                <div class="pay-card-body" style="padding-top:16px;">
                    @if($adresse)
                    <div style="display:flex;align-items:flex-start;gap:10px;margin-bottom:10px;">
                        <i class="fas fa-map-marker-alt" style="color:#16a34a;margin-top:2px;"></i>
                        <span style="font-size:.88rem;color:#374151;">{{ $adresse }}</span>
                    </div>
                    @endif
                    @if($message)
                    <div style="display:flex;align-items:flex-start;gap:10px;">
                        <i class="fas fa-comment-alt" style="color:#6b7280;margin-top:2px;"></i>
                        <span style="font-size:.88rem;color:#6b7280;">{{ $message }}</span>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>

        {{-- Colonne droite : formulaire de paiement --}}
        <div>
            <div class="pay-card">
                <div class="pay-card-header">
                    <i class="fas fa-mobile-alt" style="color:#6b7280;"></i>
                    Moyen de paiement
                </div>
                <div class="pay-card-body">

                    {{-- Sélection provider --}}
                    <div class="providers">
                        <div class="provider-card wave" id="card-wave" onclick="selectProvider('wave')">
                            <div class="provider-check"><i class="fas fa-check"></i></div>
                            <div class="wave-logo-box">Wave</div>
                            <div class="provider-name">Wave Money</div>
                            <div class="provider-tagline">Rapide & sans frais</div>
                        </div>
                        <div class="provider-card moov" id="card-moov" onclick="selectProvider('moov')">
                            <div class="provider-check"><i class="fas fa-check"></i></div>
                            <div class="moov-logo-box">Moov<br>Money</div>
                            <div class="provider-name">Moov Money</div>
                            <div class="provider-tagline">Disponible partout</div>
                        </div>
                    </div>

                    {{-- Formulaire Wave --}}
                    <form id="form-wave" class="payment-form" action="{{ route('paiement.confirmer') }}" method="POST">
                        @csrf
                        <input type="hidden" name="mode_paiement" value="wave">

                        <div class="provider-info wave">
                            <i class="fas fa-info-circle"></i>
                            <span>Entrez le numéro Wave associé à votre compte. Un code de validation vous sera demandé.</span>
                        </div>

                        <div class="field-group">
                            <label class="field-label">Numéro Wave (CI)</label>
                            <div style="position:relative;">
                                <span style="position:absolute;left:14px;top:50%;transform:translateY(-50%);color:#FF6B00;font-weight:700;font-size:.9rem;">+225</span>
                                <input type="tel" name="telephone" class="field-input wave-focus" style="padding-left:60px;"
                                    placeholder="07 XX XX XX XX" maxlength="13" required
                                    oninput="formatPhone(this)">
                            </div>
                            <div class="phone-hint"><i class="fas fa-info-circle me-1"></i>Format : 07 XX XX XX XX (Wave CI)</div>
                        </div>

                        <div class="field-group">
                            <label class="field-label">Code de confirmation (PIN)</label>
                            <div style="position:relative;">
                                <input type="text" id="pin-wave" class="field-input wave-focus pin-input"
                                    placeholder="1234" maxlength="4" inputmode="numeric" autocomplete="off"
                                    style="letter-spacing:12px;font-size:1.4rem;text-align:center;font-family:monospace;"
                                    oninput="this.value=this.value.replace(/\D/g,'')">
                            </div>
                            <div class="phone-hint"><i class="fas fa-lock me-1"></i>Code PIN à 4 chiffres (simulation)</div>
                        </div>

                        <button type="button" class="btn-pay wave" onclick="initierPaiement('wave', this)">
                            <i class="fas fa-bolt"></i>
                            Payer {{ $total > 0 ? number_format($total, 0, ',', ' ').' FCFA' : 'GRATUIT' }} via Wave
                        </button>
                        <div class="security-note">
                            <i class="fas fa-lock"></i> Simulation sécurisée — aucun vrai débit
                        </div>
                    </form>

                    {{-- Formulaire Moov --}}
                    <form id="form-moov" class="payment-form" action="{{ route('paiement.confirmer') }}" method="POST">
                        @csrf
                        <input type="hidden" name="mode_paiement" value="moov_money">

                        <div class="provider-info moov">
                            <i class="fas fa-info-circle"></i>
                            <span>Entrez le numéro Moov Money associé à votre compte.</span>
                        </div>

                        <div class="field-group">
                            <label class="field-label">Numéro Moov Money (CI)</label>
                            <div style="position:relative;">
                                <span style="position:absolute;left:14px;top:50%;transform:translateY(-50%);color:#003DA5;font-weight:700;font-size:.9rem;">+225</span>
                                <input type="tel" name="telephone" class="field-input moov-focus" style="padding-left:60px;"
                                    placeholder="01 XX XX XX XX" maxlength="13" required
                                    oninput="formatPhone(this)">
                            </div>
                            <div class="phone-hint"><i class="fas fa-info-circle me-1"></i>Format : 01 XX XX XX XX (Moov CI)</div>
                        </div>

                        <div class="field-group">
                            <label class="field-label">Code de confirmation (PIN)</label>
                            <div style="position:relative;">
                                <input type="text" id="pin-moov" class="field-input moov-focus pin-input"
                                    placeholder="1234" maxlength="4" inputmode="numeric" autocomplete="off"
                                    style="letter-spacing:12px;font-size:1.4rem;text-align:center;font-family:monospace;"
                                    oninput="this.value=this.value.replace(/\D/g,'')">
                            </div>
                            <div class="phone-hint"><i class="fas fa-lock me-1"></i>Code PIN à 4 chiffres (simulation)</div>
                        </div>

                        <button type="button" class="btn-pay moov" onclick="initierPaiement('moov', this)">
                            <i class="fas fa-mobile-alt"></i>
                            Payer {{ $total > 0 ? number_format($total, 0, ',', ' ').' FCFA' : 'GRATUIT' }} via Moov
                        </button>
                        <div class="security-note">
                            <i class="fas fa-lock"></i> Simulation sécurisée — aucun vrai débit
                        </div>
                    </form>

                    {{-- Placeholder si rien sélectionné --}}
                    <div id="no-provider" style="text-align:center;padding:24px 0;color:#9ca3af;">
                        <i class="fas fa-hand-pointer fa-2x mb-3 d-block"></i>
                        <span style="font-size:.88rem;">Sélectionnez un moyen de paiement ci-dessus</span>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

{{-- Overlay traitement --}}
<div class="processing-overlay" id="processing-overlay">
    <div class="processing-card">
        <div class="processing-spinner" id="proc-spinner"></div>
        <div class="processing-title" id="proc-title">Traitement en cours...</div>
        <div class="processing-sub" id="proc-sub">Ne fermez pas cette fenêtre</div>
        <ul class="processing-steps">
            <li>
                <div class="step-icon" id="step1-icon"><i class="fas fa-check" style="font-size:.6rem;"></i></div>
                <span>Vérification du numéro</span>
            </li>
            <li>
                <div class="step-icon" id="step2-icon"><i class="fas fa-circle" style="font-size:.4rem;"></i></div>
                <span>Validation du paiement</span>
            </li>
            <li>
                <div class="step-icon" id="step3-icon"><i class="fas fa-circle" style="font-size:.4rem;"></i></div>
                <span>Confirmation de la commande</span>
            </li>
        </ul>
    </div>
</div>
@endsection

@push('scripts')
<script>
let activeProvider = null;
let activeForm = null;

function selectProvider(provider) {
    activeProvider = provider;

    // Reset cards
    document.getElementById('card-wave').classList.remove('active');
    document.getElementById('card-moov').classList.remove('active');
    document.getElementById('card-wave').classList.toggle('active', provider === 'wave');
    document.getElementById('card-moov').classList.toggle('active', provider === 'moov');

    // Reset forms
    document.getElementById('form-wave').classList.remove('visible');
    document.getElementById('form-moov').classList.remove('visible');
    document.getElementById('no-provider').style.display = 'none';

    // Show selected form
    const form = document.getElementById('form-' + provider);
    form.classList.add('visible');
    activeForm = form;

    // Focus first input
    setTimeout(() => {
        const firstInput = form.querySelector('input[type="tel"]');
        if (firstInput) firstInput.focus();
    }, 100);
}

function formatPhone(input) {
    let v = input.value.replace(/\D/g, '');
    if (v.length > 10) v = v.slice(0, 10);
    let formatted = '';
    for (let i = 0; i < v.length; i++) {
        if (i === 2 || i === 4 || i === 6 || i === 8) formatted += ' ';
        formatted += v[i];
    }
    input.value = formatted;
}

function initierPaiement(provider, btn) {
    const form = document.getElementById('form-' + provider);

    // Validate phone
    const phone = form.querySelector('input[type="tel"]').value.replace(/\s/g, '');
    if (phone.length < 8) {
        showToast('Veuillez entrer un numéro valide.', 'error');
        return;
    }

    // Validate PIN
    const pin = form.querySelector('.pin-input').value;
    if (pin.length < 1) {
        showToast('Veuillez entrer un code PIN.', 'error');
        return;
    }

    // Show overlay
    const overlay = document.getElementById('processing-overlay');
    const spinner = document.getElementById('proc-spinner');
    const title   = document.getElementById('proc-title');
    const sub     = document.getElementById('proc-sub');

    spinner.className = 'processing-spinner ' + (provider === 'wave' ? 'wave-spin' : 'moov-spin');
    title.textContent = provider === 'wave' ? 'Traitement Wave...' : 'Traitement Moov Money...';

    overlay.classList.add('visible');
    btn.disabled = true;

    // Step animations
    const step1 = document.getElementById('step1-icon');
    const step2 = document.getElementById('step2-icon');
    const step3 = document.getElementById('step3-icon');

    step1.className = 'step-icon loading';
    step1.innerHTML = '<i class="fas fa-spinner fa-spin" style="font-size:.6rem;"></i>';

    setTimeout(() => {
        step1.className = 'step-icon done';
        step1.innerHTML = '<i class="fas fa-check" style="font-size:.6rem;"></i>';
        step2.className = 'step-icon loading';
        step2.innerHTML = '<i class="fas fa-spinner fa-spin" style="font-size:.6rem;"></i>';
    }, 1000);

    setTimeout(() => {
        step2.className = 'step-icon done';
        step2.innerHTML = '<i class="fas fa-check" style="font-size:.6rem;"></i>';
        step3.className = 'step-icon loading';
        step3.innerHTML = '<i class="fas fa-spinner fa-spin" style="font-size:.6rem;"></i>';
        title.textContent = 'Confirmation en cours...';
        sub.textContent   = 'Votre commande est en cours de validation';
    }, 2000);

    setTimeout(() => {
        step3.className = 'step-icon done';
        step3.innerHTML = '<i class="fas fa-check" style="font-size:.6rem;"></i>';
        title.textContent = 'Paiement validé !';
        sub.textContent   = 'Redirection...';
        // Submit the real form
        form.submit();
    }, 3200);
}

function showToast(msg, type) {
    const t = document.createElement('div');
    t.style.cssText = `position:fixed;top:20px;right:20px;z-index:9998;padding:12px 20px;border-radius:10px;
        font-size:.85rem;font-weight:600;color:#fff;
        background:${type === 'error' ? '#ef4444' : '#16a34a'};
        box-shadow:0 4px 20px rgba(0,0,0,.2);animation:slideIn .3s ease;`;
    t.textContent = msg;
    document.body.appendChild(t);
    setTimeout(() => t.remove(), 3000);
}

// Auto-select Wave by default
document.addEventListener('DOMContentLoaded', () => {
    selectProvider('wave');
});
</script>
@endpush
