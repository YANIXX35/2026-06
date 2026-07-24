@extends('layouts.app')
@section('title', 'Paiement confirmé !')

@section('content')
<div style="min-height:80vh;display:flex;align-items:center;justify-content:center;background:linear-gradient(135deg,#f0fdf4,#f8fafc);padding:40px 16px;">
    <div style="background:#fff;border-radius:24px;padding:40px;max-width:540px;width:100%;box-shadow:0 4px 30px rgba(0,0,0,.08);text-align:center;">

        {{-- Icône succès --}}
        <div style="width:80px;height:80px;border-radius:50%;background:linear-gradient(135deg,#16a34a,#22c55e);display:flex;align-items:center;justify-content:center;margin:0 auto 20px;box-shadow:0 8px 20px rgba(22,163,74,.3);">
            <i class="fas fa-check" style="color:#fff;font-size:2rem;"></i>
        </div>

        <span style="display:inline-flex;align-items:center;gap:6px;background:#dcfce7;border:1.5px solid #86efac;color:#15803d;font-size:.78rem;font-weight:700;padding:4px 14px;border-radius:50px;margin-bottom:14px;">
            <i class="fas fa-handshake"></i> Accord respecté
        </span>

        <h1 style="font-size:1.7rem;font-weight:900;color:#111827;margin-bottom:8px;">Paiement confirmé !</h1>
        <p style="color:#6b7280;font-size:.9rem;margin-bottom:24px;">
            Vous avez payé au prix négocié avec le fournisseur.<br>
            L'annonce reste affichée à son prix original pour les autres acheteurs.
        </p>

        {{-- Récapitulatif --}}
        <div style="background:#f8fafc;border-radius:16px;padding:20px;margin-bottom:24px;text-align:left;">
            <div style="font-size:.78rem;color:#9ca3af;font-weight:600;text-transform:uppercase;letter-spacing:.5px;margin-bottom:12px;">
                Récapitulatif
            </div>
            <div class="d-flex justify-content-between mb-2 small">
                <span class="text-muted">Produit</span>
                <span class="fw-semibold">{{ Str::limit($reservation->annonce->titre, 35) }}</span>
            </div>
            <div class="d-flex justify-content-between mb-2 small">
                <span class="text-muted">Quantité</span>
                <span class="fw-semibold">{{ $reservation->quantite_demandee }} {{ $reservation->annonce->unite }}</span>
            </div>
            <div class="d-flex justify-content-between mb-2 small">
                <span class="text-muted">Prix affiché (annonce)</span>
                <span class="text-muted" style="text-decoration:line-through;">{{ number_format($reservation->annonce->prix, 0, ',', ' ') }} FCFA</span>
            </div>
            <hr style="margin:8px 0;">
            <div class="d-flex justify-content-between fw-bold">
                <span>Prix négocié payé</span>
                <span style="color:#15803d;font-size:1.1rem;">{{ number_format($reservation->prix_negocie, 0, ',', ' ') }} FCFA</span>
            </div>
            @php
                $economie = $reservation->annonce->prix - $reservation->prix_negocie;
            @endphp
            @if($economie > 0)
            <div style="margin-top:8px;text-align:center;">
                <span style="background:#dcfce7;color:#15803d;font-size:.75rem;font-weight:700;padding:3px 12px;border-radius:50px;">
                    🎉 Vous avez économisé {{ number_format($economie, 0, ',', ' ') }} FCFA grâce à la négociation !
                </span>
            </div>
            @endif
        </div>

        {{-- Fournisseur --}}
        <div style="background:#eff6ff;border-radius:12px;padding:14px;margin-bottom:24px;font-size:.83rem;text-align:left;">
            <div class="d-flex align-items-center gap-2">
                <div style="width:36px;height:36px;border-radius:50%;background:#3b82f6;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:.85rem;flex-shrink:0;">
                    {{ strtoupper(substr($reservation->annonce->user->prenom, 0, 1)) }}
                </div>
                <div>
                    <div class="fw-semibold">{{ $reservation->annonce->user->nom_structure ?? $reservation->annonce->user->prenom . ' ' . $reservation->annonce->user->nom }}</div>
                    <div class="text-muted small"><i class="fas fa-map-marker-alt me-1"></i>{{ $reservation->annonce->adresse_collecte }}</div>
                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="d-flex gap-2 flex-wrap justify-content-center">
            <a href="{{ route('reservations.mes-reservations') }}" class="btn btn-success rounded-pill px-4 fw-semibold">
                <i class="fas fa-list me-2"></i>Mes réservations
            </a>
            <a href="{{ route('annonces.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                <i class="fas fa-store me-2"></i>Continuer les achats
            </a>
        </div>

    </div>
</div>
@endsection
