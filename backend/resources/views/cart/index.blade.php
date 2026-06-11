@extends('layouts.app')
@section('title', 'Mon panier')

@push('styles')
<style>
.cart-wrapper { max-width: 1100px; margin: 0 auto; padding: 40px 16px; }
.cart-title { font-size: 1.5rem; font-weight: 800; color: #1a1a2e; margin-bottom: 28px; display: flex; align-items: center; gap: 12px; }
.cart-grid { display: grid; grid-template-columns: 1fr 320px; gap: 24px; align-items: start; }
@media(max-width:768px){ .cart-grid { grid-template-columns: 1fr; } }

/* Item card */
.cart-item { background:#fff; border-radius:14px; box-shadow:0 1px 6px rgba(0,0,0,.07); padding:18px 20px; display:flex; gap:16px; align-items:center; margin-bottom:12px; transition:.2s; }
.cart-item:hover { box-shadow:0 4px 16px rgba(0,0,0,.1); }
.cart-item-img { width:80px; height:80px; object-fit:cover; border-radius:10px; flex-shrink:0; }
.cart-item-img-placeholder { width:80px; height:80px; background:#f3f4f6; border-radius:10px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.cart-item-info { flex:1; min-width:0; }
.cart-item-title { font-weight:700; font-size:.95rem; color:#1a1a2e; margin-bottom:4px; }
.cart-item-fournisseur { font-size:.75rem; color:#888; margin-bottom:8px; }
.cart-item-price { font-size:1rem; font-weight:800; color:#16a34a; }
.cart-item-price.gratuit { color:#3b82f6; }

/* Qty control */
.qty-control { display:flex; align-items:center; gap:0; border:1.5px solid #e5e7eb; border-radius:8px; overflow:hidden; }
.qty-btn { width:32px; height:32px; background:#f9fafb; border:none; cursor:pointer; font-size:1rem; font-weight:700; color:#555; display:flex; align-items:center; justify-content:center; transition:.15s; }
.qty-btn:hover { background:#e5e7eb; }
.qty-input { width:52px; height:32px; border:none; border-left:1.5px solid #e5e7eb; border-right:1.5px solid #e5e7eb; text-align:center; font-size:.88rem; font-weight:600; outline:none; }
.btn-remove { background:none; border:none; color:#ef4444; cursor:pointer; font-size:.8rem; padding:0; margin-left:8px; }
.btn-remove:hover { text-decoration:underline; }

/* Summary card */
.summary-card { background:#fff; border-radius:16px; box-shadow:0 1px 6px rgba(0,0,0,.08); padding:24px; position:sticky; top:20px; }
.summary-title { font-size:1rem; font-weight:700; color:#1a1a2e; margin-bottom:20px; padding-bottom:12px; border-bottom:1px solid #f0f0f0; }
.summary-line { display:flex; justify-content:space-between; font-size:.88rem; color:#555; margin-bottom:10px; }
.summary-total { display:flex; justify-content:space-between; font-size:1.1rem; font-weight:800; color:#1a1a2e; padding-top:14px; border-top:2px solid #f0f0f0; margin-top:14px; }
.btn-checkout { width:100%; background:#16a34a; color:#fff; border:none; border-radius:50px; padding:14px; font-size:.95rem; font-weight:700; cursor:pointer; transition:.2s; margin-top:20px; }
.btn-checkout:hover { background:#15803d; }
.btn-clear { width:100%; background:#fff; color:#ef4444; border:1.5px solid #fecaca; border-radius:50px; padding:10px; font-size:.82rem; font-weight:600; cursor:pointer; margin-top:10px; transition:.2s; }
.btn-clear:hover { background:#fef2f2; }

/* Checkout form inside summary */
.checkout-field { margin-bottom:12px; }
.checkout-field label { font-size:.75rem; font-weight:700; color:#888; text-transform:uppercase; display:block; margin-bottom:4px; }
.checkout-field input, .checkout-field textarea { width:100%; border:1.5px solid #e5e7eb; border-radius:8px; padding:9px 12px; font-size:.85rem; outline:none; font-family:inherit; transition:.2s; }
.checkout-field input:focus, .checkout-field textarea:focus { border-color:#16a34a; }

/* Empty state */
.empty-cart { text-align:center; padding:80px 20px; }
.empty-cart i { font-size:4rem; color:#d1d5db; display:block; margin-bottom:20px; }
.empty-cart h5 { color:#6b7280; font-size:1.1rem; margin-bottom:12px; }
</style>
@endpush

@section('content')
<div class="cart-wrapper">
    <div class="cart-title">
        <span style="background:#f0fdf4;width:44px;height:44px;border-radius:12px;display:flex;align-items:center;justify-content:center;">
            <i class="fas fa-shopping-cart" style="color:#16a34a;font-size:1.1rem;"></i>
        </span>
        Mon panier
        @if($items->count() > 0)
        <span style="background:#16a34a;color:#fff;font-size:.75rem;font-weight:700;padding:3px 10px;border-radius:50px;">{{ $items->count() }} article(s)</span>
        @endif
    </div>

    @if(session('success'))
        <div class="alert alert-success rounded-3 mb-4">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger rounded-3 mb-4">{{ session('error') }}</div>
    @endif

    @if($items->isEmpty())
    <div class="empty-cart">
        <i class="fas fa-shopping-cart"></i>
        <h5>Votre panier est vide</h5>
        <p class="text-muted mb-4">Explorez nos annonces et ajoutez des produits à votre panier.</p>
        <a href="{{ route('annonces.index') }}" class="btn btn-primary rounded-pill px-5 py-2">
            <i class="fas fa-search me-2"></i>Explorer les annonces
        </a>
    </div>
    @else
    <div class="cart-grid">
        {{-- Items --}}
        <div>
            @foreach($items as $item)
            <div class="cart-item">
                @if($item->annonce->photoPrincipale)
                    <img src="{{ $item->annonce->photoPrincipale->url }}" class="cart-item-img" alt="{{ $item->annonce->titre }}" onerror="this.src='{{ asset('img/no-image.jpg') }}'">
                @else
                    <div class="cart-item-img-placeholder">
                        <i class="fas fa-box" style="color:#9ca3af;font-size:1.5rem;"></i>
                    </div>
                @endif

                <div class="cart-item-info">
                    <div class="cart-item-title">
                        <a href="{{ route('annonces.show', $item->annonce) }}" class="text-dark text-decoration-none">{{ $item->annonce->titre }}</a>
                    </div>
                    <div class="cart-item-fournisseur">
                        <i class="fas fa-store me-1"></i>
                        {{ $item->annonce->user->nom_structure ?? ($item->annonce->user->prenom.' '.$item->annonce->user->nom) }}
                        <span class="ms-2">•</span>
                        <span class="ms-2">{{ $item->annonce->categorie->icone ?? '' }} {{ $item->annonce->categorie->nom ?? '' }}</span>
                    </div>

                    <div class="d-flex align-items-center gap-3 flex-wrap">
                        {{-- Qty form --}}
                        <form action="{{ route('cart.update', $item) }}" method="POST" class="d-flex align-items-center gap-2">
                            @csrf @method('PATCH')
                            <div class="qty-control">
                                <button type="button" class="qty-btn" onclick="changeQty(this, -1)">−</button>
                                <input type="number" name="quantite" class="qty-input" value="{{ $item->quantite }}" min="0.01" max="{{ $item->annonce->quantite }}" step="0.01">
                                <button type="button" class="qty-btn" onclick="changeQty(this, 1)">+</button>
                            </div>
                            <span style="font-size:.72rem;color:#bbb;">/ {{ $item->annonce->quantite }} {{ $item->annonce->unite }}</span>
                            <button type="submit" style="background:#f0fdf4;color:#16a34a;border:none;border-radius:6px;padding:5px 10px;font-size:.72rem;font-weight:600;cursor:pointer;">
                                <i class="fas fa-check"></i> Mettre à jour
                            </button>
                        </form>
                    </div>
                </div>

                <div class="text-end" style="flex-shrink:0;min-width:90px;">
                    <div class="cart-item-price {{ $item->annonce->type_offre === 'don' ? 'gratuit' : '' }}">
                        @if($item->annonce->type_offre === 'don')
                            GRATUIT
                        @else
                            {{ number_format($item->sousTotal(), 0, ',', ' ') }} F
                        @endif
                    </div>
                    @if($item->annonce->type_offre !== 'don')
                    <div style="font-size:.7rem;color:#bbb;margin-top:2px;">{{ number_format($item->annonce->prix, 0, ',', ' ') }} F / {{ $item->annonce->unite }}</div>
                    @endif

                    <form action="{{ route('cart.remove', $item) }}" method="POST" class="mt-2">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn-remove"><i class="fas fa-trash-alt me-1"></i>Retirer</button>
                    </form>
                </div>
            </div>
            @endforeach

            {{-- Vider le panier --}}
            <form action="{{ route('cart.vider') }}" method="POST" style="margin-top:8px;">
                @csrf
                <button type="submit" class="btn btn-link text-danger p-0 small" onclick="return confirm('Vider tout le panier ?')">
                    <i class="fas fa-trash me-1"></i>Vider le panier
                </button>
            </form>
        </div>

        {{-- Résumé + Checkout --}}
        <div>
            <div class="summary-card">
                <div class="summary-title"><i class="fas fa-receipt me-2" style="color:#16a34a;"></i>Récapitulatif</div>

                @foreach($items as $item)
                <div class="summary-line">
                    <span style="max-width:160px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $item->annonce->titre }}</span>
                    <span style="font-weight:600;">
                        @if($item->annonce->type_offre === 'don') Gratuit
                        @else {{ number_format($item->sousTotal(), 0, ',', ' ') }} F
                        @endif
                    </span>
                </div>
                @endforeach

                <div class="summary-total">
                    <span>Total</span>
                    <span style="color:#16a34a;">{{ $total > 0 ? number_format($total, 0, ',', ' ').' FCFA' : 'Gratuit' }}</span>
                </div>

                <form action="{{ route('paiement.initier') }}" method="POST" class="mt-4">
                    @csrf
                    <div class="checkout-field">
                        <label>Adresse de collecte (optionnel)</label>
                        <input type="text" name="adresse_livraison" placeholder="Ex: Cocody, Rue des jardins...">
                    </div>
                    <div class="checkout-field">
                        <label>Message aux fournisseurs</label>
                        <textarea name="message" rows="2" placeholder="Instructions particulières..."></textarea>
                    </div>
                    <button type="submit" class="btn-checkout">
                        <i class="fas fa-lock me-2"></i>Procéder au paiement
                    </button>
                </form>

                <a href="{{ route('annonces.index') }}" class="d-block text-center text-muted small mt-3 text-decoration-none">
                    <i class="fas fa-arrow-left me-1"></i>Continuer mes achats
                </a>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function changeQty(btn, delta) {
    const input = btn.parentElement.querySelector('.qty-input');
    const max = parseFloat(input.max) || 9999;
    const min = parseFloat(input.min) || 0.01;
    let val = parseFloat(input.value) + delta;
    input.value = Math.min(max, Math.max(min, val)).toFixed(2);
}
</script>
@endpush
