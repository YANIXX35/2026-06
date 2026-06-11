@extends('layouts.app')
@section('title', 'Paiement confirmé')

@push('styles')
<style>
.succes-wrapper {
    min-height: 100vh;
    background: linear-gradient(135deg, #f0fdf4 0%, #f8fafc 100%);
    display: flex; align-items: center; justify-content: center;
    padding: 40px 16px;
}
.succes-card {
    background: #fff; border-radius: 24px; max-width: 560px; width: 100%;
    box-shadow: 0 4px 40px rgba(0,0,0,.1); overflow: hidden;
}
.succes-top {
    background: linear-gradient(135deg, #16a34a, #15803d);
    padding: 40px 32px 32px; text-align: center; position: relative; overflow: hidden;
}
.succes-top::before {
    content: '';
    position: absolute; inset: 0;
    background: radial-gradient(circle at 70% 50%, rgba(255,255,255,.1) 0%, transparent 60%);
}
.succes-icon {
    width: 80px; height: 80px; border-radius: 50%;
    background: rgba(255,255,255,.2); border: 3px solid rgba(255,255,255,.4);
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 16px; position: relative; z-index: 1;
}
.succes-icon i { font-size: 2rem; color: #fff; }
.succes-title { font-size: 1.5rem; font-weight: 900; color: #fff; margin-bottom: 6px; position: relative; z-index:1; }
.succes-sub { color: rgba(255,255,255,.8); font-size: .9rem; position: relative; z-index:1; }

.succes-body { padding: 28px 32px; }

/* Reference */
.ref-box {
    background: #f0fdf4; border: 1.5px dashed #86efac; border-radius: 12px;
    padding: 14px 20px; margin-bottom: 24px; text-align: center;
}
.ref-label { font-size: .72rem; font-weight: 700; color: #6b7280; text-transform: uppercase; letter-spacing: 1px; }
.ref-value { font-size: 1.1rem; font-weight: 900; color: #16a34a; letter-spacing: 2px; font-family: monospace; }

/* Payment method badge */
.method-badge {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 6px 14px; border-radius: 50px; font-size: .8rem; font-weight: 700; margin-bottom: 20px;
}
.method-badge.wave { background: #fff8f3; color: #c2410c; border: 1px solid #fed7aa; }
.method-badge.moov { background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; }
.method-badge.gratuit { background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; }

/* Items */
.item-row {
    display: flex; justify-content: space-between; align-items: center;
    padding: 8px 0; border-bottom: 1px solid #f9fafb; font-size: .86rem;
}
.item-row:last-child { border-bottom: none; }
.item-row-name { color: #374151; font-weight: 500; }
.item-row-price { font-weight: 700; color: #16a34a; }

.total-row {
    display: flex; justify-content: space-between; align-items: center;
    padding: 14px 0 0; border-top: 2px solid #f3f4f6; margin-top: 8px;
    font-size: 1rem; font-weight: 800; color: #111827;
}

.succes-actions { display: flex; gap: 12px; margin-top: 24px; flex-wrap: wrap; }
.btn-primary-green {
    flex: 1; background: #16a34a; color: #fff; border: none; border-radius: 50px;
    padding: 13px; font-size: .88rem; font-weight: 700; cursor: pointer;
    text-decoration: none; text-align: center; transition: background .2s; min-width: 160px;
}
.btn-primary-green:hover { background: #15803d; color: #fff; }
.btn-outline-green {
    flex: 1; background: #fff; color: #16a34a; border: 2px solid #16a34a; border-radius: 50px;
    padding: 11px; font-size: .88rem; font-weight: 700; cursor: pointer;
    text-decoration: none; text-align: center; transition: all .2s; min-width: 160px;
}
.btn-outline-green:hover { background: #f0fdf4; color: #16a34a; }

/* Confetti animation */
@keyframes confetti-fall {
    0% { transform: translateY(-100px) rotate(0deg); opacity: 1; }
    100% { transform: translateY(100vh) rotate(720deg); opacity: 0; }
}
.confetti-piece {
    position: fixed; width: 10px; height: 10px; border-radius: 2px;
    animation: confetti-fall linear forwards;
    pointer-events: none; z-index: 9999;
}
</style>
@endpush

@section('content')
<div class="succes-wrapper">
    <div class="succes-card">
        <div class="succes-top">
            <div class="succes-icon">
                <i class="fas fa-check"></i>
            </div>
            <div class="succes-title">Paiement confirmé !</div>
            <div class="succes-sub">Votre commande a été passée avec succès</div>
        </div>

        <div class="succes-body">
            {{-- Référence --}}
            <div class="ref-box">
                <div class="ref-label">Référence de commande</div>
                <div class="ref-value">{{ $commande->reference_paiement }}</div>
            </div>

            {{-- Mode de paiement --}}
            <div>
                @if($commande->mode_paiement === 'wave')
                    <span class="method-badge wave">
                        <i class="fas fa-bolt"></i> Payé via Wave Money
                    </span>
                @elseif($commande->mode_paiement === 'moov_money')
                    <span class="method-badge moov">
                        <i class="fas fa-mobile-alt"></i> Payé via Moov Money
                    </span>
                @else
                    <span class="method-badge gratuit">
                        <i class="fas fa-gift"></i> Commande gratuite
                    </span>
                @endif
                @if($commande->telephone_paiement)
                    <span style="font-size:.78rem;color:#9ca3af;margin-left:8px;">
                        (+225) {{ $commande->telephone_paiement }}
                    </span>
                @endif
            </div>

            {{-- Articles --}}
            <div style="margin: 16px 0 8px; font-size:.75rem; font-weight:800; color:#9ca3af; text-transform:uppercase; letter-spacing:1px;">
                Articles commandés
            </div>
            @foreach($commande->items as $item)
            <div class="item-row">
                <span class="item-row-name">{{ Str::limit($item->annonce->titre, 40) }}</span>
                <span class="item-row-price">
                    @if($item->prix_unitaire == 0) Gratuit
                    @else {{ number_format($item->quantite * $item->prix_unitaire, 0, ',', ' ') }} F
                    @endif
                </span>
            </div>
            @endforeach

            <div class="total-row">
                <span>Total payé</span>
                <span style="color:#16a34a;">{{ $commande->montant_total > 0 ? number_format($commande->montant_total, 0, ',', ' ').' FCFA' : 'Gratuit' }}</span>
            </div>

            {{-- Info fournisseurs notifiés --}}
            <div style="background:#f8fafc;border-radius:10px;padding:12px 16px;margin-top:16px;font-size:.8rem;color:#6b7280;display:flex;align-items:center;gap:8px;">
                <i class="fas fa-bell" style="color:#16a34a;"></i>
                Les fournisseurs ont été notifiés de votre commande. Ils vous contacteront pour organiser la collecte.
            </div>

            {{-- Actions --}}
            <div class="succes-actions">
                <a href="{{ route('commandes.show', $commande) }}" class="btn-primary-green">
                    <i class="fas fa-eye me-2"></i>Voir la commande
                </a>
                <a href="{{ route('annonces.index') }}" class="btn-outline-green">
                    <i class="fas fa-search me-2"></i>Continuer les achats
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Confetti
const colors = ['#16a34a','#22c55e','#4ade80','#86efac','#FF6B00','#fbbf24','#60a5fa'];
function launchConfetti() {
    for (let i = 0; i < 60; i++) {
        setTimeout(() => {
            const el = document.createElement('div');
            el.className = 'confetti-piece';
            el.style.left = Math.random() * 100 + 'vw';
            el.style.background = colors[Math.floor(Math.random() * colors.length)];
            el.style.animationDuration = (Math.random() * 2 + 1.5) + 's';
            el.style.animationDelay = '0s';
            el.style.width = (Math.random() * 8 + 6) + 'px';
            el.style.height = (Math.random() * 8 + 6) + 'px';
            el.style.borderRadius = Math.random() > .5 ? '50%' : '2px';
            document.body.appendChild(el);
            setTimeout(() => el.remove(), 4000);
        }, i * 40);
    }
}
document.addEventListener('DOMContentLoaded', launchConfetti);
</script>
@endpush
